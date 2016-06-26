<?php

namespace AwsUtility\Tests;

class ApiGatewayTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function getRestApisReturnsExpectedArrayOfApis()
    {
        $apiGatewayClient = $this->getMockBuilder('\Aws\ApiGateway\ApiGatewayClient')
            ->disableOriginalConstructor()
            ->setMethods(['getRestApis'])
            ->getMock();

        $apiGatewayClient->method('getRestApis')->willReturn(new \Aws\Result(
            [
                'items' => [
                    ['name' => 'API-1', 'id' => 'xxxy'],
                    ['name' => 'API-2', 'id' => 'xxyy'],
                    ['name' => 'API-3', 'id' => 'xyyy'],
                ]
            ]
        ));

        $apiGatewayService = new \AwsUtility\Services\ApiGateway($apiGatewayClient);
        $apis = $apiGatewayService->getRestApis();

        $this->assertSame(3, count($apis));
        $this->assertSame('xyyy', $apis['API-3']['id']);
    }
    
    /**
     * @test
     */
    public function getResourcesReturnsExpectedArrayOfResources()
    {
        $apiGatewayClient = $this->getMockBuilder('\Aws\ApiGateway\ApiGatewayClient')
            ->disableOriginalConstructor()
            ->setMethods(['getResources'])
            ->getMock();

        $apiGatewayClient->method('getResources')->willReturn(new \Aws\Result(
            [
                'items' => [
                    ['pathPart' => 'updateTestPath', 'id' => 'xxxy'],
                    ['pathPart' => 'updateTestPath2', 'id' => 'xxyy'],
                    ['pathPart' => 'updateTestPath3', 'id' => 'xyyy'],
                    ['path' => '/updateTestPath3', 'id' => 'yyyy'],
                ]
            ]
        ));

        $apiGatewayService = new \AwsUtility\Services\ApiGateway($apiGatewayClient);
        $resources = $apiGatewayService->getResources('12345');

        $this->assertSame(3, count($resources));
        $this->assertSame('xyyy', $resources['updateTestPath3']['id']);
    }
    
    /**
     * @test
     */
    public function getEndpointReturnsExpectedEndpoint()
    {
        $apiGatewayClient = $this->getMockBuilder('\Aws\ApiGateway\ApiGatewayClient')
            ->disableOriginalConstructor()
            ->getMock();

        $apiGatewayService = new \AwsUtility\Services\ApiGateway($apiGatewayClient);
        $endpoint = $apiGatewayService->getEndpoint(['id' => 'xxy12', 'name' => 'api-name-with-dash'], 'updateTestPath3', 'eu-west-1');
        $expectedEndpoint = 'https://xxy12.execute-api.eu-west-1.amazonaws.com/api_name_with_dash_stage/updateTestPath3';
        
        $this->assertSame($expectedEndpoint, $endpoint);
    }
}
