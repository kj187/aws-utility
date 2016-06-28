<?php

namespace AwsUtility\Command\Iam\InstanceProfiles;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ListCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('iam:instance-profiles:list')
            ->setDescription('Lists the instance profiles that have the specified path prefix. If there are none, the action returns an empty list.');
        parent::configure();
    }
    
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $pathPrefix = '/*';
        if ($input->hasOption($pathPrefix)) {
            $pathPrefix = $input->getOption('pathPrefix');
        }
        
        $results = $this->iamService->listInstanceProfiles($pathPrefix);
        if (empty($results)) {
            throw new \Exception('No instance profiles available');
        }
        
        foreach ($results as $result) {
            $output->writeln('[' . $result['InstanceProfileId'] . '] ' . $result['InstanceProfileName'] . '(' . $result['Path'] . ')');
        }
    }
}
