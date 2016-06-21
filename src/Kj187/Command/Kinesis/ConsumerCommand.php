<?php

namespace Kj187\Command\Kinesis;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ConsumerCommand extends AbstractCommand
{
    /**
     * @var string
     */
    protected $assumedRoleSessionName = 'aws-utility-kinesis-consumer';
    
    protected function configure()
    {
        $this
            ->setName('kinesis:consumer')
            ->setDescription('Consume records of a Kinesis stream and show the amount of each shard');
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

        $client = $this->getClient();
        $res = $client->describeStream([ 'StreamName' => $streamName ]);

        $shardIds = $res->search('StreamDescription.Shards[].ShardId');
        $recordsInStream = 0;
        $numberOfRecordsPerBatch = 10000;

        $output->writeln('');
        $output->writeln('Shards: ' . count($shardIds));
        $output->writeln('');

        foreach ($shardIds as $shardId) {
            $output->writeln("ShardId: $shardId");

            $res = $client->getShardIterator([
                'ShardId' => $shardId,
                'ShardIteratorType' => 'TRIM_HORIZON',
                'StreamName' => $streamName,
            ]);
            $shardIterator = $res->get('ShardIterator');

            $recordsInShard = 0;
            do {
                $res = $client->getRecords([
                    'Limit' => $numberOfRecordsPerBatch,
                    'ShardIterator' => $shardIterator
                ]);

                $recordsInBatch = count($res->get('Records'));
                $recordsInShard = $recordsInShard+$recordsInBatch;
                $shardIterator = $res->get('NextShardIterator');

                usleep(200 * 1000);
            } while ($res->get('MillisBehindLatest') !== 0);

            $recordsInStream = $recordsInStream+$recordsInShard;

            $output->writeln("\t$recordsInShard records available");
        }

        $output->writeln('');
        $output->writeln('--------------------------------------------');
        $output->writeln($recordsInStream . ' records in stream');
        $output->writeln('');
    }
}