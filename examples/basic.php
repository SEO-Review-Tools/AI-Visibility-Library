<?php
require __DIR__ . '/../autoload.php';

use SRT\AIVisibility\BrandVisibilityClient;
use SRT\AIVisibility\Config;

$client = new BrandVisibilityClient(Config::fromFile(__DIR__ . '/../config/settings.php'));

// ChatGPT
$chatgpt = $client->chatgpt('Nike');

// Gemini
$gemini = $client->gemini('Nike');

// Perplexity
$perplexity = $client->perplexity('Nike');

print_r($chatgpt);
print_r($gemini);
print_r($perplexity);
