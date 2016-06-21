<?php

namespace Kj187\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Yaml\Parser;
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
