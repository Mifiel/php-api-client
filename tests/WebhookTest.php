<?php
namespace Mifiel\Tests;

use GuzzleHttp\Psr7\Response;
use Mifiel\ArgumentError;
use Mifiel\Webhook;
use Mifiel\Tests\Support\MockHttp;
use PHPUnit\Framework\TestCase;

class WebhookTest extends TestCase {
  use MockHttp;

  public function testListCreateAndDeleteWebhooks(): void {
    $this->mockResponses(
      new Response(200, [], '[{"id":"existing-id","callback_type":"document_closed"}]'),
      new Response(201, [], '{"id":"new-id","callback_type":"signer_rejected"}'),
      new Response(204)
    );
    $webhooks = Webhook::all();
    $this->assertInstanceOf(Webhook::class, $webhooks[0]);
    $this->assertSame('existing-id', $webhooks[0]->id);

    $params = ['url' => 'https://example.test/callback', 'callback_type' => 'signer_rejected'];
    $webhook = new Webhook($params);
    $webhook->save();
    $this->assertSame('new-id', $webhook->id);
    Webhook::delete($webhook->id);

    $this->assertSame(['GET', 'POST', 'DELETE'], array_map(fn($entry) => $entry['request']->getMethod(), $this->history));
    $this->assertSame('/api/v1/webhooks', $this->history[0]['request']->getUri()->getPath());
    $this->assertSame($params, json_decode((string) $this->history[1]['request']->getBody(), true));
    $this->assertSame('/api/v1/webhooks/new-id', $this->history[2]['request']->getUri()->getPath());
  }

  public function testQueuedAndInstantTriggersPreserveResponseStatusAndBody(): void {
    $this->mockResponses(new Response(202, [], '{"queued":true}'), new Response(200, [], '{"status":200}'));
    $webhook = new Webhook(['id' => 'webhook-id']);
    $queued = $webhook->trigger('document-id');
    $instant = $webhook->trigger('document-id', true);

    $this->assertSame(202, $queued->getStatusCode());
    $this->assertTrue(json_decode((string) $queued->getBody())->queued);
    $this->assertSame(200, $instant->getStatusCode());
    $this->assertSame(200, json_decode((string) $instant->getBody())->status);
    foreach ([false, true] as $index => $expected) {
      $request = $this->history[$index]['request'];
      $this->assertSame('POST', $request->getMethod());
      $this->assertSame('/api/v1/webhooks/webhook-id/trigger', $request->getUri()->getPath());
      $this->assertSame(['resource' => 'document-id', 'instant' => $expected], json_decode((string) $request->getBody(), true));
    }
  }

  public function testTriggerRequiresAWebhookId(): void {
    $this->mockResponses();
    $this->expectException(ArgumentError::class);
    (new Webhook())->trigger('document-id');
  }
}
