<?php

namespace Kj187;

use Symfony\Component\Yaml\Parser;

class Settings {
    
    /**
     * @var array
     */
    protected $settings = [];

    /**
     * @return array
     * @throws \Exception
     */
    public function getSettings()
    {
        if (empty($this->settings)) {
            $file = ROOT_DIR . 'configuration/settings.yaml';

            if (!is_file($file)) {
                throw new \Exception('Configuration file not available. Expected file is ' . $file);
            }

            $yamlParser = new Parser();
            $this->settings = $yamlParser->parse(file_get_contents($file));
        }
        
        return $this->settings;
    }

    /**
     * @param string $settingKey
     * @return string
     * @throws \Exception
     */
    public function get($settingKey)
    {
        $keys = explode('.', trim($settingKey));
        $settings = $this->getSettings();
        
        foreach ($keys as $key) {
            if (!isset($settings[$key])) {
                continue;
            }
            
            if (is_array($settings[$key])) {
                $settings = $settings[$key];
                continue;
            }
            
            return $settings[$key];
        }
        
        throw new \Exception('Setting with key ' . $settingKey . ' not available');
    }
}
