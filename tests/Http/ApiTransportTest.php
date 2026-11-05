<?php
namespace Mifiel\Tests\Http;

use GuzzleHttp\Psr7\Response;
use Mifiel\ApiClient;
use Mifiel\Document;
use Mifiel\Tests\Support\MockHttp;
use PHPUnit\Framework\TestCase;

class ApiTransportTest extends TestCase {
  use MockHttp;

  public function testMultipartPreservesExplicitFalseAndZeroValues(): void {
    $this->mockResponses(new Response(201, [], '{"id":"document-id"}'));
    $document = new Document([
      'file_path' => __DIR__ . '/../fixtures/example.pdf',
      'send_invites' => false,
      'remind_every' => 0,
      'external_id' => '0',
      'message_for_signers' => '',
      'viewers' => [],
      'unused' => null,
      'signatories' => [[
        'email' => 'signer@example.test',
        'allowed_signature_methods' => ['FEA', 'FESCV'],
      ]],
    ]);
    $document->save();

    $request = $this->history[0]['request'];
    $body = (string) $request->getBody();
    $this->assertStringContainsString('multipart/form-data; boundary=', $request->getHeaderLine('Content-Type'));
    foreach (['send_invites' => '0', 'remind_every' => '0', 'external_id' => '0', 'message_for_signers' => '', 'viewers' => '[]'] as $name => $value) {
      $this->assertMatchesRegularExpression('/name="' . $name . '"\r\n(?:[^\r\n]+\r\n)*\r\n' . preg_quote($value, '/') . '\r\n/', $body);
    }
    $this->assertStringNotContainsString('name="unused"', $body);
    $this->assertStringContainsString('filename="example.pdf"', $body);
    $this->assertStringContainsString(file_get_contents(__DIR__ . '/../fixtures/example.pdf'), $body);
    $this->assertStringContainsString('"allowed_signature_methods":["FEA","FESCV"]', $body);
    $this->assertSame('document-id', $document->id);
    $this->assertSigned($request);
  }

  public function testGetParametersAreEncodedInTheSignedQueryString(): void {
    $this->mockResponses(new Response(200, [], '[]'));
    ApiClient::get('documents', ['page' => 2, 'name' => 'Contrato A&B']);

    $request = $this->history[0]['request'];
    parse_str($request->getUri()->getQuery(), $query);
    $this->assertSame(['page' => '2', 'name' => 'Contrato A&B'], $query);
    $this->assertSame('', (string) $request->getBody());
    $this->assertSigned($request);
  }

  public function testJsonRetainsFalseAndUsesTheSignedRequestBody(): void {
    $this->mockResponses(new Response(200, [], '{}'));
    ApiClient::post('webhooks/webhook-id/trigger', ['resource' => 'document-id', 'instant' => false]);

    $request = $this->history[0]['request'];
    $this->assertSame('application/json', $request->getHeaderLine('Content-Type'));
    $this->assertSame(['resource' => 'document-id', 'instant' => false], json_decode((string) $request->getBody(), true));
    $this->assertSigned($request);
  }

  private function assertSigned(\Psr\Http\Message\RequestInterface $request): void {
    $this->assertNotEmpty($request->getHeaderLine('Date'));
    $canonical = implode(',', [
      $request->getMethod(),
      strtolower($request->getHeaderLine('Content-Type')),
      $request->getHeaderLine('Content-MD5'),
      $request->getRequestTarget(),
      $request->getHeaderLine('Date'),
    ]);
    $expected = 'APIAuth test-app-id:' . base64_encode(hash_hmac('sha1', $canonical, 'test-secret', true));
    $this->assertSame($expected, $request->getHeaderLine('Authorization'));
  }
}
