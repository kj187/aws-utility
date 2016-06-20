<?php

namespace Kj187\Command\Kinesis;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Yaml\Parser;

class AbstractCommand extends Command
{
    /**
     * @var \Aws\Kinesis\KinesisClient
     */
    protected $_kinesisClient = null;

    /**
     * @var string
     */
    protected $_region = '';

    /**
     * @var array
     */
    protected $_settings = [];

    /**
     * @param string $name
     */
    public function __construct($name = null)
    {
        $settings = $this->_getSettings();
        $this->_region = $settings['kinesis']['defaults']['region'];
        
        parent::__construct($name);
    }

    /**
     * @return array
     */
    protected function _getSettings()
    {
        if (empty($this->_settings)) {
            $file = __DIR__ . '/../../../../configuration/settings.yaml';
            $yamlParser = new Parser();
            $this->_settings = $yamlParser->parse(file_get_contents($file));
        }

        return $this->_settings;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws \Exception
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        if ($region = $input->getOption('region')) {
            $this->_region = $region;
        }

        $streams = $this->_findAllStreamNames();

        $helper = $this->getHelper('question');
        $question = new ChoiceQuestion('Please select a stream', $streams);
        $question->setErrorMessage('Stream %s is invalid.');

        $streamName = $helper->ask($input, $output, $question);
        $output->writeln('Selected stream: ' . $streamName);

        $input->setArgument('streamName', $streamName);
    }

    /**
     * @return array
     * @throws \Exception
     */
    protected function _findAllStreamNames()
    {
        $client = $this->_getKinesisClient();
        $streams = $client->listStreams();

        if (!isset($streams['StreamNames'])) {
            throw new \Exception('No Kinesis streams available');
        }

        return $streams['StreamNames'];
    }

    /**
     * @return \Aws\Kinesis\KinesisClient
     */
    protected function _getKinesisClient()
    {
        if ($this->_kinesisClient === null) {
            $sdk = new \Aws\Sdk();
            $this->_kinesisClient = $sdk->createKinesis(['region' => $this->_region, 'version' => '2013-12-02']);
        }

        return $this->_kinesisClient;
    }
}
