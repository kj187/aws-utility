<?php

namespace Kj187;

use Symfony\Component\Yaml\Parser;

class Settings {
    
    /**
     * @var array
     */
    protected static $settings = [];
    
    /**
     * @return array
     */
    public static function getSettings()
    {
        if (empty(self::$settings)) {
            $file = __DIR__ . '/../../configuration/settings.yaml';
            $yamlParser = new Parser();
            self::$settings = $yamlParser->parse(file_get_contents($file));
        }
        
        return self::$settings;
    }
    
    /**
     * @param string
     * @return string
     */    
    public static function get($settingKey)
    {
        $keys = explode('.', trim($settingKey));
        $settings = self::getSettings();
        
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
