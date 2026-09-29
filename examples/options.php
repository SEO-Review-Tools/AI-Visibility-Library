<?php
require __DIR__ . '/../autoload.php';

use SRT\AIVisibility\BrandVisibilityClient;
use SRT\AIVisibility\Config;

$client = new BrandVisibilityClient(Config::fromFile(__DIR__ . '/../config/settings.php'));

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
$report = $client->extract($response);

// The extracted structure includes both convenient fields and the complete response.
echo $report['brand_name'] . PHP_EOL;
echo $report['brand_description'] . PHP_EOL;
echo 'Awareness score: ' . (isset($report['brand_awareness_analysis']['brand_awareness_score']) ? $report['brand_awareness_analysis']['brand_awareness_score'] : '') . PHP_EOL;
echo 'Sentiment score: ' . (isset($report['sentiment_analysis']['sentiment_score']) ? $report['sentiment_analysis']['sentiment_score'] : '') . PHP_EOL;
echo 'Credibility score: ' . (isset($report['credibility_analysis']['credibility_score']) ? $report['credibility_analysis']['credibility_score'] : '') . PHP_EOL;

// Every field returned by the API remains available here.
print_r($report['raw_response']);
