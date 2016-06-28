<?php

namespace AwsUtility\Command\Iam\InstanceProfiles;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AbstractCommand extends \AwsUtility\Command\AbstractCommand
{
    /**
     * @var \AwsUtility\Services\Iam
     */
    protected $iamService = null;
    
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */    
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);

        $sdk = new \Aws\Sdk();
        $client = $sdk->createIam(
            [
                'region' => $this->getRegion(),
                'version' => $this->settings->get('services.iam.version'),
                'credentials' => $this->getCredentials()
            ]
        );
        $this->iamService = new \AwsUtility\Services\Iam($client);
    }    
    
    protected function configure()
    {
        $this
            ->addOption(
                'pathPrefix',
                null,
                InputOption::VALUE_OPTIONAL,
                'The path prefix for filtering the results. For example, the prefix /application_abc/component_xyz/ gets all instance profiles whose path starts with /application_abc/component_xyz/.'
            );
            parent::configure();
    }
}
