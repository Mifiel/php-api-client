<?php

declare(strict_types=1);

namespace Mifiel\Http;

use Closure;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\RequestInterface;

/**
 * Guzzle middleware that signs requests using the APIAuth HMAC protocol.
 * Replicates the logic from the legacy acquia/http-hmac-php 2.x ApiAuthGemDigest.
 *
 * Guzzle stacks middleware in two phases (see {@link https://docs.guzzlephp.org/en/stable/handlers-and-middleware.html}):
 * 1. **Composition (once):** {@see self::__invoke()} receives the next handler and returns a closure.
 *    This factory runs only when {@see \GuzzleHttp\HandlerStack::resolve()} first builds the chain.
 * 2. **Per request:** That closure runs for every HTTP call and delegates to {@see signRequest()}.
 *
 * To debug signing, set breakpoints on {@see signRequest()} (or the private closure below), not only on
 * {@see __invoke()} — the outer method is not invoked on every request.
 */
final class HmacAuthMiddleware
{
    private const PROVIDER = 'APIAuth';
    private const DEFAULT_CONTENT_TYPE = 'application/json';

    public function __construct(
        private readonly string $appId,
        private readonly string $appSecret
    ) {
    }

    /**
     * Middleware factory: Guzzle invokes this once per {@see HandlerStack} when composing the stack,
     * not once per HTTP request.
     *
     * @param callable(RequestInterface, array): PromiseInterface $handler
     *
     * @return callable(RequestInterface, array): PromiseInterface
     */
    public function __invoke(callable $handler): Closure
    {
        return function (RequestInterface $request, array $options) use ($handler): PromiseInterface {
            return $this->sendSignedRequest($handler, $request, $options);
        };
    }

    /**
     * Runs on every outbound request after the stack is composed.
     *
     * @param callable(RequestInterface, array): PromiseInterface $handler
     */
    private function sendSignedRequest(callable $handler, RequestInterface $request, array $options): PromiseInterface
    {
        return $handler($this->signRequest($request), $options);
    }

    public function signRequest(RequestInterface $request): RequestInterface
    {
        if (!$request->hasHeader('Date')) {
            $request = $request->withHeader(
                'Date',
                gmdate('D, d M Y H:i:s \G\M\T')
            );
        }

        if (!$request->hasHeader('Content-Type')) {
            $request = $request->withHeader('Content-Type', self::DEFAULT_CONTENT_TYPE);
        }

        $message = $this->buildMessage($request);
        $signature = base64_encode(hash_hmac('sha1', $message, $this->appSecret, true));
        $authorization = self::PROVIDER . ' ' . str_replace(':', '\\:', $this->appId) . ':' . $signature;

        return $request->withHeader('Authorization', $authorization);
    }

    private function buildMessage(RequestInterface $request): string
    {
        $method = strtoupper($request->getMethod());
        $contentType = strtolower($request->getHeaderLine('Content-Type'));
        $contentMd5 = $request->hasHeader('content-md5') ? $request->getHeaderLine('content-md5') : '';
        $resource = $request->getRequestTarget();
        $timestamp = $request->getHeaderLine('Date');

        return implode(',', [$method, $contentType, $contentMd5, $resource, $timestamp]);
    }
}
