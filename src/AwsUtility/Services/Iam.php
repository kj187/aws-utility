<?php

namespace AwsUtility\Services;

class Iam {

    /**
     * @var \Aws\Iam\IamClient
     */
    protected $client = null;
    
    /**
     * @param \Aws\Iam\IamClient $client
     */
    public function __construct(\Aws\Iam\IamClient $client)
    {
        $this->client = $client;
    }

    /**
     * @param string $pathPrefix
     * @return array
     */
    public function listInstanceProfiles($pathPrefix = '/*')
    {
        $result = $this->client->listInstanceProfiles(['PathPrefix' => $pathPrefix]);
        return $result['InstanceProfiles'];
    }
}
