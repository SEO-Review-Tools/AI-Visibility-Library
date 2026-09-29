# AI Visibility Library

Reusable PHP library for the SEO Review Tools AI Visibility APIs for **ChatGPT, Google Gemini, and Perplexity**.

## Included

- Shared PHP client for all three model endpoints.
- API key and runtime settings page.
- Optional location, competitor, and product/service inputs.
- cURL-based HTTPS requests with timeout and SSL verification.
- API error handling through `ApiException`.
- `extract()` helper for convenient access to the documented report fields.
- Complete decoded API response preserved under `raw_response` and `report` so additional fields can be consumed without changing the library.
- PHP calling examples.

The current v3 endpoints documented by SEO Review Tools are:

- ChatGPT: `https://api.seoreviewtools.com/v3/chatgpt-brand-research/`
- Gemini: `https://api.seoreviewtools.com/v3/gemini-brand-research/`
- Perplexity: `https://api.seoreviewtools.com/v3/perplexity-brand-research/`

Each endpoint currently documents a price of 3 credits per request. The ChatGPT, Gemini and Perplexity documentation describes the same general response structure, including brand description, awareness/visibility, sentiment, credibility, products/services, related prompts/topics and competitor analysis.

## Installation

Copy the `ai visibility library` folder to your PHP project/server.

```php
require '/path/to/ai visibility library/autoload.php';

use SRT\AIVisibility\BrandVisibilityClient;
use SRT\AIVisibility\Config;

$client = new BrandVisibilityClient(
    Config::fromFile('/path/to/ai visibility library/config/settings.php')
);
```

PHP 8.1+ is recommended. The cURL extension is required.

## Settings

Open:

`public/settings.php`

Save your API key, default location, timeout and SSL setting. For production, protect this page with your own authentication or remove it after configuring the library.

Alternatively, configure the client directly:

```php
$client = new BrandVisibilityClient([
    'api_key' => 'YOUR-API-KEY',
    'base_url' => 'https://api.seoreviewtools.com/v3/',
    'timeout' => 60,
    'verify_ssl' => true,
    'default_location' => 'United States',
]);
```

## Basic calls

```php
$chatgpt = $client->chatgpt('Nike');
$gemini = $client->gemini('Nike');
$perplexity = $client->perplexity('Nike');
```

All three calls return the complete decoded API response.

## Optional inputs

The current v3 documentation supports these optional parameters:

```php
$options = [
    'location' => 'United States',
    'competitors' => [
        'https://www.adidas.com',
        'https://www.underarmour.com',
        'https://us.puma.com/',
    ],
    'products_or_services' => [
        'sneakers',
        'running shoes',
        'basketball shoes',
    ],
];

$response = $client->chatgpt('Nike', $options);
```

The API documentation specifies a minimum of 3 and maximum of 5 competitors and a minimum of 3 and maximum of 10 products/services when those optional inputs are supplied.

## Extracting report data

```php
$report = $client->extract($response);

echo $report['brand_name'];
echo $report['company_url'];
echo $report['brand_awareness_analysis']['brand_awareness_score'];
echo $report['sentiment_analysis']['sentiment_score'];
echo $report['credibility_analysis']['credibility_score'];
```

Useful arrays include:

- `brand_awareness_analysis`
- `sentiment_analysis`
- `credibility_analysis`
- `services_products`
- `top_5_topics`
- `related_prompts`
- `top_5_competitors`

For forward compatibility, the complete report is also available as `$report['report']` and the complete outer API response as `$report['raw_response']`.

## Examples

- `examples/basic.php` — call all three models.
- `examples/options.php` — use location, competitors and products/services and extract fields.
- `examples/error-handling.php` — handle API/application errors.

## API documentation

ChatGPT: https://api.seoreviewtools.com/documentation/brand-monitor-apis/chatgpt-brand-visibility-report-api/

Gemini: https://api.seoreviewtools.com/documentation/brand-monitor-apis/gemini-brand-visibility-report-api/

Perplexity: https://api.seoreviewtools.com/documentation/brand-monitor-apis/perplexity-brand-visibility-report-api/
