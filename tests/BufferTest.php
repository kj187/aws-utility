<?php

class BufferTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function addCountMatchesCallbackDataCount()
    {
        $buffer = new \AwsUtility\Buffer(function($data){
            $this->assertEquals(3, count($data));
        }, 3);
        $buffer->add('one');
        $buffer->add('two');
        $buffer->add('three');
        $buffer->add('four');
    }
}
