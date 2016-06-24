<?php

namespace AwsUtility\Command\Kinesis;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ProducerCommand extends AbstractCommand
{
    /**
     * @var string
     */
    protected $assumedRoleSessionName = 'aws-utility-kinesis-producer';
    
    protected function configure()
    {
        $this
            ->setName('kinesis:producer')
            ->setDescription('Push records of JSON files to a Kinesis stream');
        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $streamName = $input->getArgument('streamName');
        $output->writeln('');

        $buffer = new \AwsUtility\Buffer(function(array $data) use ($streamName, $output) {
            $output->writeln('Flushing');
            $result = $this->kinesisService->putRecords($streamName, $data);
            $output->writeln('Failed records: ' . $result->get('FailedRecordCount'));
        });

        // TODO interact, read folder and ask user which one should be used for mocks
        foreach (new \DirectoryIterator($this->settings->get('services.kinesis.producer.mocks.path')) as $fileInfo) {
            if($fileInfo->isDot()) continue;
            $fileContent = file_get_contents($fileInfo->getPathName());
            $buffer->add($fileContent);
        }

        $buffer->flush();
    }
}
