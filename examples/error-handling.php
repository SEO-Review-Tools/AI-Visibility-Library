<?php
require __DIR__ . '/../autoload.php';

use SRT\AIVisibility\ApiException;
use SRT\AIVisibility\BrandVisibilityClient;
use SRT\AIVisibility\Config;

$client = new BrandVisibilityClient(Config::fromFile(__DIR__ . '/../config/settings.php'));

try {
    $response = $client->perplexity('Nike');
    $report = $client->extract($response);
    echo json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;
} catch (ApiException $e) {
    echo 'API error: ' . $e->getMessage() . PHP_EOL;
    echo 'HTTP status: ' . $e->getHttpStatus() . PHP_EOL;
    echo json_encode($e->getResponse(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;
} catch (Throwable $e) {
    echo 'Application error: ' . $e->getMessage() . PHP_EOL;
}
