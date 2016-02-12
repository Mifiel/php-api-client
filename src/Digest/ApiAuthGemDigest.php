<?php
namespace Mifiel\Digest;

use Acquia\Hmac\RequestSignerInterface;
use Acquia\Hmac\Request\RequestInterface;
use Acquia\Hmac\Digest\Version1;

class ApiAuthGemDigest extends Version1 {
  protected function getMessage(RequestSignerInterface $requestSigner, RequestInterface $request)
  {
    $parts = array(
      $this->getMethod($request),
      $this->getContentType($requestSigner, $request),
      $this->getHashedBody($request),
      $this->getResource($request),
      $this->getTimestamp($requestSigner, $request)
    );
    echo "\n" . join(',', $parts) . "\n";
    return join(',', $parts);
  }
}
