<?php

namespace Kj187;

class CommandRegistry
{
    public static function getCommands()
    {
        return [
            new \Kj187\Command\Kinesis\ConsumerCommand(),
            new \Kj187\Command\Kinesis\ProducerCommand(),
            new \Kj187\Command\ApiGateway\ProducerCommand()
        ];
    }
}
