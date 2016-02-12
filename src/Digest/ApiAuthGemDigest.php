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

    return join(',', $parts);
  }

  protected function getHashedBody(RequestInterface $request) {
    if ($request->hasHeader('content-md5')){
      return $request->getHeader('content-md5');
    }
    return md5($request->getBody());
  }

  protected function getTimestamp(RequestSignerInterface $requestSigner, RequestInterface $request) {
    if ($request->hasHeader('Date')){
      return $request->getHeader('Date');
    }
    return $requestSigner->getTimestamp($request);
  }
}

