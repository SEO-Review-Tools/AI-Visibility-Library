<?php
namespace SRT\AIVisibility;

final class Config
{
    private $settings;

    public function __construct(array $settings = array())
    {
        $this->settings = array_merge(array(
            'api_key' => '',
            'base_url' => 'https://api.seoreviewtools.com/v3/',
            'timeout' => 60,
            'verify_ssl' => true,
            'default_location' => '',
        ), $settings);
    }

    public static function fromFile($file)
    {
        if (!is_file($file)) {
            return new self();
        }
        $settings = require $file;
        return new self(is_array($settings) ? $settings : array());
    }

    public function get($key, $default = null)
    {
        return isset($this->settings[$key]) ? $this->settings[$key] : $default;
    }

    public function all()
    {
        return $this->settings;
    }
}
