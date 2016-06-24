<?php

namespace AwsUtility\Command\Kinesis;

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

        $shardIds = $this->kinesisService->findAllShardIds($streamName);
        $recordsInStream = 0;

        $output->writeln('');
        $output->writeln('Shards: ' . count($shardIds));
        $output->writeln('');

        foreach ($shardIds as $shardId) {
            $output->writeln("ShardId: $shardId");
            $recordsInShard = $this->kinesisService->getShardRecordCount($shardId, $streamName);
            $recordsInStream = $recordsInStream+$recordsInShard;
            $output->writeln("\t$recordsInShard records available");
        }

        $output->writeln('');
        $output->writeln('--------------------------------------------');
        $output->writeln($recordsInStream . ' records in stream');
        $output->writeln('');
    }
}