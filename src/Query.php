<?php
namespace Mifiel;

/** Advanced searches across account resources. */
class Query {
  /**
   * Return records and pagination metadata as provided by the API.
   *
   * @see https://docs.mifiel.com/en/#operation/QueryEndpoint
   * @return object
   */
  public static function execute(array $params) {
    BaseObject::checkRequiredArgs(['fields' => 'string', 'resource' => 'string'], $params);
    // This endpoint is outside the /api/v1/ prefix used by other resources.
    $response = ApiClient::post('/api/query', $params);
    return json_decode((string) $response->getBody(), false, 512, JSON_THROW_ON_ERROR);
  }
}
