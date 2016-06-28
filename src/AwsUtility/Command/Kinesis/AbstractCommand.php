<?php

namespace AwsUtility\Command\Kinesis;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

class AbstractCommand extends \AwsUtility\Command\AbstractCommand
{
    /**
     * @var \AwsUtility\Services\Kinesis
     */
    protected $kinesisService = null;
    
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */    
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);

        $sdk = new \Aws\Sdk();
        $client = $sdk->createKinesis(
            [
                'region' => $this->getRegion(),
                'version' => $this->settings->get('services.kinesis.version'),
                'credentials' => $this->getCredentials()
            ]
        );
        $this->kinesisService = new \AwsUtility\Services\Kinesis($client);
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
        $streams = $this->kinesisService->findAllStreamNames();

        $helper = $this->getHelper('question');
        $question = new ChoiceQuestion('Please select a stream', $streams);
        $question->setErrorMessage('Stream %s is invalid.');

        $streamName = $helper->ask($input, $output, $question);
        $output->writeln('Selected stream: ' . $streamName);

        $input->setArgument('streamName', $streamName);
    }
}
