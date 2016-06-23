<?php

namespace AwsUtility\Command\Kinesis;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Yaml\Parser;

class AbstractCommand extends \AwsUtility\Command\AbstractCommand
{
    /**
     * @var \Aws\Kinesis\KinesisClient
     */
    protected $client = null;
    
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */    
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);
    }
    
    protected function configure()
    {
        $this
            ->addArgument(
                'streamName',
                InputArgument::REQUIRED,
                'Stream Name'
            );
            parent::configure();
    }
    
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws \Exception
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $streams = $this->findAllStreamNames();

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
    protected function findAllStreamNames()
    {
        $client = $this->getClient();
        $streams = $client->listStreams();

        if (!isset($streams['StreamNames'])) {
            throw new \Exception('No Kinesis streams available');
        }

        return $streams['StreamNames'];
    }

    /**
     * @return \Aws\Kinesis\KinesisClient
     */
    protected function getClient()
    {
        if ($this->client === null) {
            $sdk = new \Aws\Sdk();
            $this->client = $sdk->createKinesis(
                [
                    'region' => $this->getRegion(), 
                    'version' => $this->settings->get('services.kinesis.version'),
                    'credentials' => $this->getCredentials()
                ]
            );
        }

        return $this->client;
    }
}
