# Improvely API PHP SDK

Server-side HTTP API that you can use to perform account management tasks and to add conversion events that do not occur on your website, but can still be linked to a specific website visitor, such as recurring bills.

## Installation

`composer require lukapeharda/improvely`

## Requirements

Make sure to find your [Improvely API key](https://www.improvely.com/docs/api-introduction) and project ID.

## Usage

### Conversions

Docs for conversions can be found [here](https://www.improvely.com/docs/api-post-conversion). Make sure to check required params, its combos and all other attributes.

`key` and `project` attributes will be auto inserted, all others can be set freely.

In order to be able to record a conversion, you need to [label your visitor](https://www.improvely.com/docs/labeling-visitors) beforehand.

Recording a conversion:

```php
...
$apiKey = '#YOUR_API_KEY#';
$projectID = '#YOUR_PROJECT_ID#';

$client = new LukaPeharda\Improvely\Conversion($apiKey, $projectId);

$params = [
    'label' => '#LABELED_VISITOR_ID_OR_EMAIL_OR_USERNAME#', // this value depends on what you've used to label your user with
    'goal' => 'purchase',
    'revenue' => 19.99,
];

$response = $client->record($params);
...
```
