<?php

namespace AwsUtility\Security;

class TokenService {

    /**
     * @var \Aws\Result
     */
    protected $result = null;

    /**
     * Returns a set of temporary security credentials (consisting of an access key ID,
     * a secret access key, and a security token) that you can use to access AWS resources
     * that you might not normally have access to.
     *
     * http://docs.aws.amazon.com/aws-sdk-php/v3/api/api-sts-2011-06-15.html#assumerole
     *
     * @param \Aws\Sts\StsClient $stsClient
     * @param string $roleArn The Amazon Resource Name (ARN) of the role to assume.
     * @param string $roleSessionName An identifier for the assumed role session.
     * @param string $externalId A unique identifier that is used by third parties when assuming roles in their customers' accounts.
     * @return \Aws\Result
     * @throws \Exception
     */
    public function __construct(\Aws\Sts\StsClient $stsClient, $roleArn, $roleSessionName, $externalId = '')
    {
        $result = $stsClient->assumeRole([
            'RoleArn' => $roleArn,
            'RoleSessionName' => $roleSessionName,
            'ExternalId' => $externalId
        ]);

        if (!$result->hasKey('Credentials')) {
            throw new \Exception('Credentials not found in sts:assumeRole response');
        }
        
        $this->result = $result;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        $token = $this->result['Credentials']['SessionToken'];
        return $token;
    }
    
    /**
     * @return string
     */
    public function getAwsAccessKeyId()
    {
        $awsAccessKeyId = $this->result['Credentials']['AccessKeyId'];
        return $awsAccessKeyId;
    }
    
    /**
     * @return string
     */
    public function getAwsSecretAccessKey()
    {
        $awsSecretAccessKey = $this->result['Credentials']['SecretAccessKey'];
        return $awsSecretAccessKey;
    }    
}
