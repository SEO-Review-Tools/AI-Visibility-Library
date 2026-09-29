<?php
namespace SRT\AIVisibility;

final class BrandVisibilityClient
{
    private $config;

    private const ENDPOINTS = [
        'chatgpt' => 'chatgpt-brand-research/',
        'gemini' => 'gemini-brand-research/',
        'perplexity' => 'perplexity-brand-research/',
    ];

    public function __construct($config = null)
    {
        $this->config = $config instanceof Config
            ? $config
            : new Config(is_array($config) ? $config : []);
    }

    public function getReport($model, $brandInput, array $options = array())
    {
        $model = strtolower(trim($model));
        if (!isset(self::ENDPOINTS[$model])) {
            throw new \InvalidArgumentException('Unsupported model. Use chatgpt, gemini, or perplexity.');
        }
        if (trim($brandInput) === '') {
            throw new \InvalidArgumentException('brandInput cannot be empty.');
        }

        $params = [
            'brandinput' => $brandInput,
            'key' => isset($options['api_key']) ? $options['api_key'] : $this->config->get('api_key'),
        ];

        if ((isset($options['location']) ? $options['location'] : $this->config->get('default_location')) !== '') {
            $params['location'] = isset($options['location']) ? $options['location'] : $this->config->get('default_location');
        }
        foreach (['competitors', 'products_or_services'] as $key) {
            if (array_key_exists($key, $options) && $options[$key] !== null && $options[$key] !== '') {
                $params[$key] = is_array($options[$key]) ? implode('|', $options[$key]) : $options[$key];
            }
        }

        if (!$params['key']) {
            throw new \InvalidArgumentException('No API key configured.');
        }

        return $this->request(self::ENDPOINTS[$model], $params);
    }

    public function chatgpt($brandInput, array $options = array())
    {
        return $this->getReport('chatgpt', $brandInput, $options);
    }

    public function gemini($brandInput, array $options = array())
    {
        return $this->getReport('gemini', $brandInput, $options);
    }

    public function perplexity($brandInput, array $options = array())
    {
        return $this->getReport('perplexity', $brandInput, $options);
    }

    /**
     * Extracts the model report while preserving the complete API response.
     * Nothing is discarded: raw_response contains the exact decoded API payload.
     */
    public function extract(array $response)
    {
        $outer = isset($response['data']) ? $response['data'] : [];
        $report = isset($outer['data']) ? $outer['data'] : [];

        return [
            'status' => isset($response['status']) ? $response['status'] : null,
            'result' => isset($response['result']) ? $response['result'] : null,
            'tool_name' => isset($outer['tool name']) ? $outer['tool name'] : null,
            'source' => isset($outer['source']) ? $outer['source'] : null,
            'input' => isset($outer['input']) ? $outer['input'] : [],
            'brand_name' => isset($report['brand_name']) ? $report['brand_name'] : null,
            'brand_description' => isset($report['brand_description']) ? $report['brand_description'] : null,
            'company_url' => isset($report['company_url']) ? $report['company_url'] : null,
            'brand_awareness_analysis' => isset($report['brand_awareness_analysis']) ? $report['brand_awareness_analysis'] : [],
            'sentiment_analysis' => isset($report['sentiment_analysis']) ? $report['sentiment_analysis'] : [],
            'credibility_analysis' => isset($report['credibility_analysis']) ? $report['credibility_analysis'] : [],
            'services_products' => isset($report['services_products']) ? $report['services_products'] : [],
            'top_5_topics' => isset($report['top_5_topics']) ? $report['top_5_topics'] : [],
            'related_prompts' => isset($report['related_prompts']) ? $report['related_prompts'] : [],
            'top_5_competitors' => isset($report['top_5_competitors']) ? $report['top_5_competitors'] : [],
            'report' => $report,
            'raw_response' => $response,
        ];
    }

    private function request($endpoint, array $params)
    {
        $base = rtrim((string)$this->config->get('base_url'), '/') . '/';
        $url = $base . ltrim($endpoint, '/') . '?' . http_build_query($params, '', '&', PHP_QUERY_RFC3986);

        if (!function_exists('curl_init')) {
            throw new ApiException('PHP cURL extension is required.');
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_CONNECTTIMEOUT => 15,
            CURLOPT_TIMEOUT => (int)$this->config->get('timeout', 60),
            CURLOPT_HTTPGET => true,
            CURLOPT_HTTPHEADER => ['Accept: application/json'],
            CURLOPT_SSL_VERIFYPEER => (bool)$this->config->get('verify_ssl', true),
            CURLOPT_SSL_VERIFYHOST => (bool)$this->config->get('verify_ssl', true) ? 2 : 0,
            CURLOPT_USERAGENT => 'SRT AI Visibility Library/1.0',
        ]);

        $body = curl_exec($ch);
        $errno = curl_errno($ch);
        $error = curl_error($ch);
        $httpStatus = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($body === false || $errno) {
            throw new ApiException('HTTP request failed: ' . ($error ?: 'Unknown cURL error'), [], $httpStatus);
        }

        $decoded = json_decode($body, true);
        if (!is_array($decoded)) {
            throw new ApiException('The API returned invalid JSON.', ['raw_body' => $body], $httpStatus);
        }

        if ((isset($decoded['status']) ? $decoded['status'] : null) !== 'ok') {
            $message = isset($decoded['error message']) ? $decoded['error message'] : (isset($decoded['message']) ? $decoded['message'] : 'The API returned an error.');
            throw new ApiException($message, $decoded, $httpStatus);
        }

        return $decoded;
    }
}
