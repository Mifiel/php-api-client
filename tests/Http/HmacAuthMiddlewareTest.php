<?php

declare(strict_types=1);

namespace Mifiel\Tests\Http;

use GuzzleHttp\Promise\Create;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Mifiel\Http\HmacAuthMiddleware;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

class HmacAuthMiddlewareTest extends TestCase
{
    public function testSignRequestUsesKnownValidSignatureWithFixedDate(): void
    {
        $middleware = new HmacAuthMiddleware(
            'b041efb8db49308e496b70e3bdf51d0005c184d3',
            'M5DHRLCdYCqOs2PELvizW6qr/yXIBj0CAAk9/OR8UbwadFjBeeKn774sf3IVO7G8H2wUJsLsn3uL3H8SJCdd4w=='
        );

        // Fixed Date makes the canonical string deterministic.
        $request = new Request(
            'GET',
            'http://localhost:3000/api/tests/hmac-auth',
            ['Date' => 'Mon, 06 Apr 2026 16:07:00 GMT', 'Content-Type' => 'application/json']
        );

        $signedRequest = $middleware->signRequest($request);

        $this->assertSame(
            'APIAuth b041efb8db49308e496b70e3bdf51d0005c184d3:0xQTp1drrXNR4ZFyLQSOteNAcmA=',
            $signedRequest->getHeaderLine('Authorization')
        );
    }

    public function testInvokePassesSignedRequestToNextHandler(): void
    {
        $middleware = new HmacAuthMiddleware(
            'b041efb8db49308e496b70e3bdf51d0005c184d3',
            'M5DHRLCdYCqOs2PELvizW6qr/yXIBj0CAAk9/OR8UbwadFjBeeKn774sf3IVO7G8H2wUJsLsn3uL3H8SJCdd4w=='
        );

        $capturedAuthorization = null;

        $nextHandler = function (RequestInterface $request, array $options) use (&$capturedAuthorization) {
            $capturedAuthorization = $request->getHeaderLine('Authorization');
            return Create::promiseFor(new Response(200));
        };

        $handler = $middleware($nextHandler);

        $request = new Request(
            'GET',
            'http://localhost:3000/api/tests/hmac-auth',
            ['Date' => 'Mon, 06 Apr 2026 16:07:00 GMT', 'Content-Type' => 'application/json']
        );

        $handler($request, [])->wait();

        $this->assertSame(
            'APIAuth b041efb8db49308e496b70e3bdf51d0005c184d3:0xQTp1drrXNR4ZFyLQSOteNAcmA=',
            $capturedAuthorization
        );
    }
}

