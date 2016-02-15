<?php
namespace Mifiel\Digest;

use Acquia\Hmac\RequestSignerInterface;
use Acquia\Hmac\Request\RequestInterface;
use Acquia\Hmac\Digest\Version1;

class ApiAuthGemDigest extends Version1 {
  protected function getMessage(RequestSignerInterface $requestSigner, RequestInterface $request) {

    $parts = array(
      $this->getMethod($request),
      $this->getContentType($requestSigner, $request),
      $this->getHashedBody($request),
      $this->getResource($request),
      $this->getTimestamp($requestSigner, $request)
    );

    $message = join(',', $parts);
    return $message;
  }

  protected function getHashedBody(RequestInterface $request) {
    if ($request->hasHeader('content-md5')){
      return $request->getHeader('content-md5');
    }

    $body = $request->getBody();
    // return base64_encode(md5($body->__toString()));

    // TODO: test if json first
    $contents = json_encode($body);
    // for some reason this works
    $response = $contents == "{}" ? '' : md5($contents, true);
    return base64_encode($response);
  }

  protected function getTimestamp(RequestSignerInterface $requestSigner, RequestInterface $request) {
    return $requestSigner->getTimestamp($request);
  }
}
