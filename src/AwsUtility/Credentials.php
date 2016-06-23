<?php

namespace AwsUtility;

class Credentials extends \Aws\Credentials\Credentials {

    /**
     * Constructs a new BasicAWSCredentials object, with the specified AWS
     * access key and AWS secret key
     *
     * @param string $key AWS access key ID
     * @param string $secret AWS secret access key
     * @param string $token Security token to use
     * @param int $expires UNIX timestamp for when credentials expire
     */
    public function __construct($key, $secret, $token = null, $expires = null)
    {
        parent::__construct($this->getAwsAccessKeyId($key), $this->getAwsSecretAccessKey($secret), $token, $expires);
    }
    
    /**
     * @param string $awsAccessKeyId
     */
    private function getAwsAccessKeyId($awsAccessKeyId)
    {
        if (!$awsAccessKeyId && !empty(getenv('AWS_ACCESS_KEY_ID'))) {
            $awsAccessKeyId = getenv('AWS_ACCESS_KEY_ID');
        }
        
        if (!$awsAccessKeyId) {
            throw new \Exception('No AWS Access Key ID available. Please add this as --awsAccessKeyId or as ENV var AWS_ACCESS_KEY_ID');
        }
        
        return $awsAccessKeyId;
    }
    
    /**
     * @param string $awsSecretAccessKey
     */
    private function getAwsSecretAccessKey($awsSecretAccessKey)
    {
        if (!$awsSecretAccessKey && !empty(getenv('AWS_SECRET_ACCESS_KEY'))) {
            $awsSecretAccessKey = getenv('AWS_SECRET_ACCESS_KEY');
        }
        
        if (!$awsSecretAccessKey) {
            throw new \Exception('No AWS Secret Access Key available. Please add this as --awsSecretAccessKey or as ENV var AWS_SECRET_ACCESS_KEY');
        }
        
        return $awsSecretAccessKey;
    }    
}
