<?php

namespace Kj187\Command\Kinesis;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ProducerCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('kinesis:producer')
            ->setDescription('Push records of JSON files to a Kinesis stream')
            ->addArgument(
                'streamName',
                InputArgument::REQUIRED,
                'Stream Name'
            )
            ->addOption(
                'region',
                null,
                InputOption::VALUE_OPTIONAL,
                'Region to which the client is configured to send requests'
            )
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($region = $input->getOption('region')) {
            $this->_region = $region;
        }

        $streamName = $input->getArgument('streamName');

        $output->writeln('');
        $client = $this->_getKinesisClient();

        $buffer = new \Kj187\Buffer(function(array $data) use ($client, $streamName, $output) {
            $output->writeln('Flushing');
            $parameter = [ 'StreamName' => $streamName, 'Records' => []];
            foreach ($data as $item) {
                $parameter['Records'][] = ['Data' => $item, 'PartitionKey' => md5($item)];
            }
            $res = $client->putRecords($parameter);
            $output->writeln('Failed records: ' . $res->get('FailedRecordCount'));
        });

        foreach (new \DirectoryIterator($this->_getSettings()['kinesis']['producer']['mocks']['path']) as $fileInfo) {
            if($fileInfo->isDot()) continue;
            $fileContent = file_get_contents($fileInfo->getPathName());
            $buffer->add($fileContent);
        }

        $buffer->flush();
    }
}
