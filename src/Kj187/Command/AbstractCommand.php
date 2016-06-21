<?php

namespace Kj187\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AbstractCommand extends Command
{
    /**
     * @var string
     */
    protected $region = '';

    /**
     * @var array
     */
    protected $settings = [];
    
    /**
     * @var \Aws\Credentials\CredentialsInterface
     */
    protected $credentials = null;

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */    
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);
        
        $this->settings = $this->getSettings();
        $this->region = $this->settings['defaults']['region'];

        if ($region = $input->getOption('region')) {
            $this->region = $region;
        }
        
        $this->credentials = new \Kj187\Credentials($input->getOption('awsAccessKeyId'), $input->getOption('awsSecretAccessKey'));
    }

    /**
     * @return \Aws\Credentials\CredentialsInterface
     */
    protected function getCredentials()
    {
        return $this->credentials;
    }

    protected function configure()
    {
        $this
            ->addOption(
                'region',
                null,
                InputOption::VALUE_OPTIONAL,
                'Region to which the client is configured to send requests'
            )
            ->addOption(
                'awsAccessKeyId',
                null,
                InputOption::VALUE_OPTIONAL,
                'AWS access key'
            )
            ->addOption(
                'awsSecretAccessKey',
                null,
                InputOption::VALUE_OPTIONAL,
                'AWS secret key'
            );                        
    }

    /**
     * @return array
     */
    protected function getSettings()
    {
        if (empty($this->settings)) {
            $file = __DIR__ . '/../../../configuration/settings.yaml';
            $yamlParser = new Parser();
            $this->settings = $yamlParser->parse(file_get_contents($file));
        }

        return $this->settings;
    }
    
    /**
     * @return string
     */
    protected function getRegion()
    {
        return $this->region;
    }
}
