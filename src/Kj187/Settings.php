<?php

namespace Kj187;

use Symfony\Component\Yaml\Parser;

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
