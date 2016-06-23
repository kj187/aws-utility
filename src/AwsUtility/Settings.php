<?php

namespace AwsUtility;

use Symfony\Component\Yaml\Parser;

/**
 * Settings
 *
 * Usage: $settings = new \AwsUtility\Settings()
 * To get a single value just use: $settings->get('level1.level2.level3.firstname')
 * To get a nested array just use: $settings->get('level1.level2')
 */
class Settings {
    
    /**
     * @var array
     */
    protected $settings = [];

    /**
     * @param string $file
     */
    public function __construct($file)
    {
        if (!is_file($file)) {
            throw new \Exception('Configuration file "' . $file . '" not available. Expected file is configuration/settings.yaml');
        }

        $yamlParser = new Parser();
        $this->settings = $yamlParser->parse(file_get_contents($file));
    }

    /**
     * @param string $settingKey
     * @return string
     * @throws \Exception
     */
    public function get($settingKey)
    {
        $keys = explode('.', trim($settingKey));
        $keysCount = count($keys);
        $settings = $this->settings;
        $iterator = 1;
        
        foreach ($keys as $key) {
            if (!isset($settings[$key])) {
                continue;
            }
            
            if (is_array($settings[$key]) && $iterator != $keysCount) {
                $settings = $settings[$key];
                $iterator++;
                continue;
            }
            
            return $settings[$key];
        }
        
        throw new \Exception('Setting with key ' . $settingKey . ' not available');
    }
}
