<?php
namespace Mifiel;

/**
 * Account-level webhook subscriptions.
 *
 * @see https://docs.mifiel.com/en/#tag/Webhooks
 */
class Webhook extends BaseObject {
  protected static $resourceName = 'webhooks';

  /**
   * Trigger delivery for this webhook.
   *
   * @param string $resource UUID of the related resource included in the callback payload.
   * @param bool   $instant  When true, deliver immediately once instead of enqueueing retries.
   */
  public function trigger($resource, $instant = false) {
    if (!$this->id) {
      throw new ArgumentError('Webhook id is required to trigger', 1);
    }

    return ApiClient::post(
      static::$resourceName . '/' . $this->id . '/trigger',
      [
        'resource' => $resource,
        'instant' => $instant,
      ],
      false
    );
  }
}
