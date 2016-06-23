<?php

class CredentialsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function isCredentialsObjectInstanceOfAwsCredentialsInterface()
    {
        $credentials = new \AwsUtility\Security\Credentials('x', 'y');
        $this->assertInstanceOf('\Aws\Credentials\CredentialsInterface', $credentials);
    }

    /**
     * @test
     */
    public function usingEnvironment()
    {
        putenv('AWS_ACCESS_KEY_ID=test123');
        putenv('AWS_SECRET_ACCESS_KEY=321tset');

        $credentials = new \AwsUtility\Security\Credentials();
        $this->assertSame('test123', $credentials->getAccessKeyId());
        $this->assertSame('321tset', $credentials->getSecretKey());

        putenv('AWS_ACCESS_KEY_ID=');
        putenv('AWS_SECRET_ACCESS_KEY=');
    }

    /**
     * @test
     */
    public function missingAwsAccessKeyIdThrowsException()
    {
        try {
            $credentials = new \AwsUtility\Security\Credentials('');
        } catch (\Exception $e) {
            $this->assertSame('No AWS Access Key ID available. Please add this as --awsAccessKeyId or as ENV var AWS_ACCESS_KEY_ID', $e->getMessage());
        }
    }

    /**
     * @test
     */
    public function missingAwsSecretAccessKeyIdThrowsException()
    {
        try {
            $credentials = new \AwsUtility\Security\Credentials('x', '');
        } catch (\Exception $e) {
            $this->assertSame('No AWS Secret Access Key available. Please add this as --awsSecretAccessKey or as ENV var AWS_SECRET_ACCESS_KEY', $e->getMessage());
        }
    }
}
