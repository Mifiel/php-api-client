# PHP API examples

The document-state, stakeholder, ZIP and query helpers below require the upcoming
4.2.0 release. Use the [API reference](https://docs.mifiel.com/en/) for current
request fields, response schemas and account-specific feature availability.

## Configure the client

```php
require 'vendor/autoload.php';

use Mifiel\ApiClient;
use Mifiel\Document;
use Mifiel\Query;
use Mifiel\Webhook;

ApiClient::setTokens('APP_ID', 'APP_SECRET');
ApiClient::url('https://app-sandbox.mifiel.com/api/v1/');
```

Production is the default after `setTokens()`. Configure the sandbox URL **after**
setting credentials. Sandbox and production credentials are different.

## Create a document with explicit options

```php
$document = new Document([
  'file_path' => 'contract.pdf',
  'signatories' => [[
    'email' => 'signer@example.com',
    'name' => 'Signer',
    'allowed_signature_methods' => ['FEA', 'FESCV'],
  ]],
  'viewers' => [['email' => 'observer@example.com']],
  'send_invites' => false,
  'remind_every' => 0,
]);
$document->save();
$documentId = $document->id;
```

Explicit `false` and `0` values are transmitted. Nested fields, including
`allowed_signature_methods` and `viewers`, pass through to the API.

## Read document state

You can reference a document by ID without making an additional fetch:

```php
$document = new Document(['id' => $documentId]);
$state = $document->getState();
echo $state->state;
foreach ($state->signing_groups as $group) {
  foreach ($group->stakeholders as $stakeholder) {
    // Inspect the returned stakeholder fields.
  }
}
```

`getState()` returns a decoded object and does not replace the document's existing
attributes. New document methods require a document ID.

## Manage stakeholders

```php
$signer = $document->addStakeholder([
  'email' => 'second-signer@example.com',
  'type' => 'signer',
  'allowed_signature_methods' => ['FEA'],
]);
$stakeholderId = $signer->id;
$widgetId = $signer->widget_id;

$updated = $document->updateStakeholder($stakeholderId, [
  'email' => 'updated-signer@example.com',
  'type' => 'signer',
  'send_invite' => false,
]);

// Only delete a participant when that is the intended action:
$response = $document->deleteStakeholder($stakeholderId);
echo $response->getStatusCode(); // Sandbox currently returns 205.
```

Creation and update require `email` and `type` (`signer` or `reviewer`) according
to the current schema. The server enforces which changes are allowed based on
document and participant state. Add/update return decoded stakeholder objects.
Deletion preserves the server's response status. In sandbox, deletion returns
205 and the stakeholder remains in document state with `state: "deleted"`.

## Download the completed archive

```php
$document->saveZIP('/path/to/contract.zip');
// Existing alternatives:
$document->saveFileSigned('/path/to/contract.pdf');
$document->saveXML('/path/to/contract.xml');
```

Downloads can return HTTP 423 while the signed files are unavailable. Subscribe
to `document_closed` to learn when signatures and the conservation certificate
are complete. `saveZIP()` writes the response bytes to the supplied path;
it does not extract the archive.

## Search with filtering and pagination

```php
$result = Query::execute([
  'resource' => 'document',
  'fields' => 'id,file_name,state',
  'q' => ['file_name' => ['cont' => 'Contrato']],
  'page' => 1,
  'per_page' => 25,
  'sort' => 'created_at-desc',
  'version' => '1.0.0',
]);
foreach ($result->records as $record) {
  echo $record->id;
}
```

`Query::execute()` requires `resource` and `fields`. It makes one request and
returns the complete decoded response, retaining pagination metadata unchanged.
It does not automatically fetch every page. The helper uses `/api/query`, as
defined in the [OpenAPI specification](https://docs.mifiel.com/en/api.json),
outside the usual `/api/v1/` prefix. This route has been verified in sandbox.
Pagination metadata is returned unchanged, preserving the server's field names.

## Manage account webhooks

Webhook support was introduced in 4.1.0:

```php
$webhooks = Webhook::all();
$webhook = new Webhook([
  'url' => 'https://your-service.example/webhooks/mifiel',
  'callback_type' => 'document_closed',
]);
$webhook->save();

// These calls deliver callbacks to the configured receiver:
$queued = $webhook->trigger($documentId);       // Normally 202, queued delivery.
$instant = $webhook->trigger($documentId, true); // Normally 200, one immediate attempt.

// When this subscription is no longer wanted:
Webhook::delete($webhook->id);
```

The supported callback types are `document_closed`, `signer_completed`,
`signer_rejected` and `document_deleted`. Trigger methods return PSR-7 responses
so callers can inspect the HTTP status and body.

## Template results and errors

`Document::createFromTemplate()` and `createManyFromTemplate()` now return the
PSR-7 response. Their arguments and request paths are unchanged. Read the result
with `json_decode((string) $response->getBody())`.

The HTTP client continues to throw Guzzle exceptions for failed API requests.
New methods that decode responses throw `JsonException` for malformed JSON.
ZIP writes throw `RuntimeException` if the destination cannot be written.
