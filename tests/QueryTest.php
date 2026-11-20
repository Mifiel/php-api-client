<?php
namespace Mifiel\Tests;

use GuzzleHttp\Psr7\Response;
use Mifiel\ApiClient;
use Mifiel\ArgumentError;
use Mifiel\Query;
use Mifiel\Tests\Support\MockHttp;
use PHPUnit\Framework\TestCase;

class QueryTest extends TestCase {
  use MockHttp;

  public function testSearchUsesRootApiRouteAndPreservesPaginationMetadata(): void {
    // Preserve metadata keys exactly as returned by the API.
    $this->mockResponses(new Response(200, [], '{"records":[{"id":"document-id"}],"_metadata":{"page":2,"total_count":26}}'));
    $params = [
      'resource' => 'document',
      'fields' => 'id,file_name,state',
      'q' => ['file_name' => ['cont' => 'Contrato']],
      'page' => 2,
      'per_page' => 25,
      'sort' => 'created_at-desc',
      'version' => '1.0.0',
    ];
    $result = Query::execute($params);

    $this->assertSame('document-id', $result->records[0]->id);
    $this->assertSame(2, $result->_metadata->page);
    $this->assertSame(26, $result->_metadata->total_count);
    $request = $this->history[0]['request'];
    $this->assertSame('POST', $request->getMethod());
    $this->assertSame('https://example.test/api/query', (string) $request->getUri());
    $this->assertSame($params, json_decode((string) $request->getBody(), true));
  }

  public function testSearchWorksWithTheSandboxBaseUri(): void {
    $this->mockResponses(new Response(200, [], '{"records":[],"___metadata":{"total_count":0}}'));
    // Set the URL before replacing the network handler, as applications do.
    ApiClient::url('https://app-sandbox.mifiel.com/api/v1/');
    $stack = ApiClient::getClient()->getConfig('handler');
    $stack->setHandler(new \GuzzleHttp\Handler\MockHandler([new Response(200, [], '{"records":[],"___metadata":{"total_count":0}}')]));
    $stack->push(\GuzzleHttp\Middleware::history($this->history));
    $result = Query::execute(['resource' => 'document', 'fields' => 'id']);

    $this->assertSame('https://app-sandbox.mifiel.com/api/query', (string) $this->history[0]['request']->getUri());
    $this->assertSame([], $result->records);
    $this->assertSame(0, $result->___metadata->total_count);
  }

  public function testSearchRequiresFieldsAndResource(): void {
    $this->mockResponses();
    $this->expectException(ArgumentError::class);
    Query::execute(['fields' => 'id']);
  }

  public function testMalformedJsonIsReported(): void {
    $this->mockResponses(new Response(200, [], 'not json'));
    $this->expectException(\JsonException::class);
    Query::execute(['resource' => 'document', 'fields' => 'id']);
  }
}
