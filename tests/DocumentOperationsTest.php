<?php
namespace Mifiel\Tests;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Response;
use Mifiel\ArgumentError;
use Mifiel\Document;
use Mifiel\Tests\Support\MockHttp;
use PHPUnit\Framework\TestCase;

class DocumentOperationsTest extends TestCase {
  use MockHttp;

  public function testGetStatePreservesSigningGroupsAndStakeholders(): void {
    $this->mockResponses(new Response(200, [], '{"id":"document-id","state":"pending","signing_groups":[{"stakeholders":[{"id":"signer-id","signed":false}]}]}'));
    $document = new Document(['id' => 'document-id', 'state' => 'old-state']);
    $state = $document->getState();

    $this->assertSame('pending', $state->state);
    $this->assertFalse($state->signing_groups[0]->stakeholders[0]->signed);
    $this->assertSame('old-state', $document->state);
    $this->assertSame('GET', $this->history[0]['request']->getMethod());
    $this->assertSame('/api/v1/documents/document-id/state', $this->history[0]['request']->getUri()->getPath());
  }

  public function testAddStakeholderSendsSignatureMethodsAndReturnsWidgetId(): void {
    $this->mockResponses(new Response(201, [], '{"id":"stakeholder-id","widget_id":"widget-id","type":"signer"}'));
    $params = ['email' => 'signer@example.test', 'type' => 'signer', 'allowed_signature_methods' => ['FEA', 'FESCV', 'FESSV']];
    $stakeholder = (new Document(['id' => 'document-id']))->addStakeholder($params);

    $this->assertSame('stakeholder-id', $stakeholder->id);
    $this->assertSame('widget-id', $stakeholder->widget_id);
    $request = $this->history[0]['request'];
    $this->assertSame('POST', $request->getMethod());
    $this->assertSame('/api/v1/documents/document-id/stakeholders', $request->getUri()->getPath());
    $this->assertSame($params, json_decode((string) $request->getBody(), true));
  }

  public function testUpdateStakeholderKeepsExplicitSendInviteFalse(): void {
    $this->mockResponses(new Response(200, [], '{"id":"stakeholder-id","email":"reviewer@example.test","type":"reviewer"}'));
    $params = ['email' => 'reviewer@example.test', 'type' => 'reviewer', 'send_invite' => false];
    $stakeholder = (new Document(['id' => 'document-id']))->updateStakeholder('stakeholder-id', $params);

    $this->assertSame('reviewer', $stakeholder->type);
    $request = $this->history[0]['request'];
    $this->assertSame('PUT', $request->getMethod());
    $this->assertSame('/api/v1/documents/document-id/stakeholders/stakeholder-id', $request->getUri()->getPath());
    $this->assertSame($params, json_decode((string) $request->getBody(), true));
  }

  /** @dataProvider deletionStatuses */
  public function testDeleteStakeholderPreservesTheServerResponseStatus(int $status): void {
    $this->mockResponses(new Response($status));
    $response = (new Document(['id' => 'document-id']))->deleteStakeholder('stakeholder-id');

    $this->assertSame($status, $response->getStatusCode());
    $this->assertSame('DELETE', $this->history[0]['request']->getMethod());
    $this->assertSame('/api/v1/documents/document-id/stakeholders/stakeholder-id', $this->history[0]['request']->getUri()->getPath());
  }

  public static function deletionStatuses(): array {
    return [[204], [205]];
  }

  public function testSaveZipWritesTheExactArchiveBytes(): void {
    $archive = "PK\x03\x04\x00\xffarchive";
    $this->mockResponses(new Response(200, ['Content-Type' => 'application/zip'], $archive));
    $path = tempnam(sys_get_temp_dir(), 'mifiel-zip-');
    try {
      (new Document(['id' => 'document-id']))->saveZIP($path);
      $this->assertSame($archive, file_get_contents($path));
      $this->assertSame('/api/v1/documents/document-id/zip', $this->history[0]['request']->getUri()->getPath());
    } finally {
      unlink($path);
    }
  }

  public function testMissingDocumentIdIsRejectedBeforeSending(): void {
    $this->mockResponses();
    foreach (['getState' => [], 'saveZIP' => ['unused.zip'], 'addStakeholder' => [[]], 'updateStakeholder' => ['stakeholder-id', []], 'deleteStakeholder' => ['stakeholder-id']] as $method => $args) {
      try {
        (new Document())->$method(...$args);
        $this->fail('Expected missing document id to be rejected');
      } catch (ArgumentError $error) {
        $this->assertSame('Document id is required', $error->getMessage());
      }
    }
    $this->assertCount(0, $this->history);
  }

  public function testMissingStakeholderIdIsRejectedBeforeSending(): void {
    $this->mockResponses();
    $document = new Document(['id' => 'document-id']);
    foreach (['updateStakeholder' => ['', []], 'deleteStakeholder' => ['']] as $method => $args) {
      try {
        $document->$method(...$args);
        $this->fail('Expected missing stakeholder id to be rejected');
      } catch (ArgumentError $error) {
        $this->assertSame('Stakeholder id is required', $error->getMessage());
      }
    }
    $this->assertCount(0, $this->history);
  }

  public function testStakeholderCreationRequiresEmailAndType(): void {
    $this->mockResponses();
    $this->expectException(ArgumentError::class);
    (new Document(['id' => 'document-id']))->addStakeholder(['email' => 'signer@example.test']);
  }

  public function testDownloadFailureDoesNotOverwriteAnExistingFile(): void {
    $this->mockResponses(new Response(423, [], '{"error":"Document is not ready"}'));
    $path = tempnam(sys_get_temp_dir(), 'mifiel-zip-');
    file_put_contents($path, 'existing file');
    try {
      (new Document(['id' => 'document-id']))->saveZIP($path);
      $this->fail('Expected the API error to propagate');
    } catch (ClientException $error) {
      $this->assertSame(423, $error->getResponse()->getStatusCode());
      $this->assertSame('existing file', file_get_contents($path));
    } finally {
      unlink($path);
    }
  }
}
