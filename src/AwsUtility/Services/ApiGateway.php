<?php

namespace AwsUtility\Services;

class ApiGateway {

    const SERVICE_NAME = 'execute-api';

    /**
     * @var \Aws\ApiGateway\ApiGatewayClient
     */
    protected $client = null;
    
    /**
     * @param \Aws\ApiGateway\ApiGatewayClient $client
     */
    public function __construct(\Aws\ApiGateway\ApiGatewayClient $client)
    {
        $this->client = $client;
    }
    
    /**
     * @return array
     */
    public function getRestApis()
    {
        $restApis = $this->client->getRestApis([]);
        
        $data = [];
        foreach ($restApis['items'] as $api) {
            $data[$api['name']] = $api;
        }

        return $data;
    }
    
    /**
     * @param string $restApiId
     * @return array
     */
    public function getResources($restApiId)
    {
        $resources = $this->client->getResources(['restApiId' => $restApiId]);
        
        $data = [];
        foreach ($resources['items'] as $resource) {
            if (!isset($resource['pathPart'])) {
                continue;
            }
            $data[$resource['pathPart']] = $resource;
        }
        
        return $data;
    }
    
    /**
     * @param array $restApi
     * @param string $resourcePathPart
     * @param string $region
     * @return string
     */
    public function getEndpoint(array $restApi, $resourcePathPart, $region)
    {
        $stageName = $this->getStageName($restApi['name']);
        $host = [$restApi['id'], self::SERVICE_NAME, $region, 'amazonaws.com'];
        $endpoint = 'https://' . implode('.', $host) . '/' . $stageName . '/' . $resourcePathPart;
        return $endpoint;
    }
    
    /**
     * @param string $stageName
     * @return string
     */
    protected function getStageName($stageName)
    {
        $stageName = str_replace('-', '_', $stageName);
        $stageName = $stageName . '_stage';
        return $stageName;
    }
}
