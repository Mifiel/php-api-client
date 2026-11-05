<?php
namespace Mifiel\Tests\Support;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Middleware;
use Mifiel\ApiClient;
use Psr\Http\Message\ResponseInterface;

trait MockHttp {
  private array $history = [];

  private function mockResponses(ResponseInterface ...$responses): void {
    ApiClient::setTokens('test-app-id', 'test-secret');
    ApiClient::url('https://example.test/api/v1/');
    $this->history = [];
    $stack = ApiClient::getClient()->getConfig('handler');
    $stack->setHandler(new MockHandler($responses));
    $stack->push(Middleware::history($this->history));
  }
}
