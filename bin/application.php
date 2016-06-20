#!/usr/bin/env php
<?php

require_once 'vendor/autoload.php';

use Symfony\Component\Console\Application;

$application = new Application('AWS Utility');
foreach (\Kj187\CommandRegistry::getCommands() as $command) {
    $application->add($command);
}
$application->run();
