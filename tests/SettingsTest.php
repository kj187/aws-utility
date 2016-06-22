<?php

define('ROOT_DIR', str_replace(basename(__DIR__), '', __DIR__));

class SettingsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function missingSettingsKey()
    {
        try {
            $settings = new \Kj187\Settings();
            $settings->get('test.test2');
        } catch (\Exception $e) {
            $this->assertEquals('Setting with key test.test2 not available', $e->getMessage());
        }
    }
}
