<?php

namespace AwsUtility\Tests;

class TokenServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function missingCredentialsThrowsException()
    {
        $stsClient = $this->getMockBuilder('\Aws\Sts\StsClient')
            ->disableOriginalConstructor()
            ->setMethods(['assumeRole'])
            ->getMock();

        $stsClient->method('assumeRole')->willReturn(new \Aws\Result([]));

        try {
            $securityTokenService = new \AwsUtility\Security\TokenService($stsClient, '', '');
        } catch (\Exception $e) {
            $this->assertSame('Credentials not found in sts:assumeRole response', $e->getMessage());
        }
    }

    /**
     * @test
     */
    public function assumeRoleReturnsExpectedCredentials()
    {
        $stsClient = $this->getMockBuilder('\Aws\Sts\StsClient')
            ->disableOriginalConstructor()
            ->setMethods(['assumeRole'])
            ->getMock();

        $stsClient->method('assumeRole')->willReturn(new \Aws\Result(
            [
                'Credentials' => [
                    'AccessKeyId' => '123',
                    'SecretAccessKey' => '321',
                    'SessionToken' => 987
                ]
            ]
        ));

        $securityTokenService = new \AwsUtility\Security\TokenService($stsClient, '', '');
        $this->assertSame('123', $securityTokenService->getAwsAccessKeyId());
        $this->assertSame('321', $securityTokenService->getAwsSecretAccessKey());
        $this->assertSame(987, $securityTokenService->getToken());
    }
}
