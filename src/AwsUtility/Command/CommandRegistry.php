<?php

namespace AwsUtility\Command;

class CommandRegistry
{
    public static function getCommands()
    {
        return [
            new \AwsUtility\Command\Kinesis\ConsumerCommand(),
            new \AwsUtility\Command\Kinesis\ProducerCommand(),
            new \AwsUtility\Command\ApiGateway\ProducerCommand(),
            new \AwsUtility\Command\Iam\InstanceProfiles\ListCommand()
        ];
    }
}
