<?php

namespace AwsUtility\Tests;

class SettingsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function missingSettingsFile()
    {
        try {
            $file = 'doesNotExist.yaml';
            $settings = new \AwsUtility\Settings($file);
        } catch (\Exception $e) {
            $this->assertSame('Configuration file "' . $file . '" not available. Expected file is configuration/settings.yaml', $e->getMessage());
        }
    }
    
    /**
     * @test
     */
    public function missingSettingsKey()
    {
        try {
            $settings = new \AwsUtility\Settings(FIXTURE_ROOT . '/settings.yaml');
            $settings->get('test.test2');
        } catch (\Exception $e) {
            $this->assertSame('Setting with key test.test2 not available', $e->getMessage());
        }
    }
    
    /**
     * @test
     */
    public function getReturnsExpectedNestedValue()
    {
        $settings = new \AwsUtility\Settings(FIXTURE_ROOT . '/settings.yaml');
        $firstname = $settings->get('level1.level2.level3.firstname');
        $this->assertSame('Julian', $firstname);
        
        $lastname = $settings->get('level1.level2.level3.lastname');
        $this->assertSame('Kleinhans', $lastname);
    }
    
    /**
     * @test
     */
    public function getReturnsExpectedNestedArray()
    {
        $settings = new \AwsUtility\Settings(FIXTURE_ROOT . '/settings.yaml');
        $data = $settings->get('level1.level2.level3b');
        $this->assertEquals(2, count($data));
        $this->assertSame('kj187', $data['social']['twitter']);
    }
}
