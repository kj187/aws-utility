<?php

namespace AwsUtility\Services;

class Kinesis {

    const MAX_EMPTY_MILLISBEHINDLATEST_SUCCESSIVELY_DURATIONS = 30;

    /**
     * @var \Aws\Kinesis\KinesisClient
     */
    protected $client = null;
    
    /**
     * @var int
     */
    protected $numberOfRecordsPerBatch = 10000;

    /**
     * @param \Aws\Kinesis\KinesisClient $client
     */
    public function __construct(\Aws\Kinesis\KinesisClient $client)
    {
        $this->client = $client;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function findAllStreamNames()
    {
        $streams = $this->client->listStreams();

        if (!isset($streams['StreamNames'])) {
            throw new \Exception('No Kinesis streams available');
        }

        return $streams['StreamNames'];
    }
    
    /**
     * @param string $streamName
     * @return array
     */
    public function findAllShardIds($streamName)
    {
        $results = $this->client->describeStream([ 'StreamName' => $streamName ]);
        $shardIds = $results->search('StreamDescription.Shards[].ShardId');
        return $shardIds;
    }
    
    /**
     * @param string $shardId
     * @param string $streamName
     * @return int
     */
    public function getShardRecordCount($shardId, $streamName)
    {
        $result = $this->client->getShardIterator([
            'ShardId' => $shardId,
            'ShardIteratorType' => 'TRIM_HORIZON',
            'StreamName' => $streamName,
        ]);

        $shardIterator = $result->get('ShardIterator');
        $recordsInShard = 0;
        $emptyDurationCount = 0;
        $emptyMillisBehindLatestSuccessivelyDurationCount = 0;

        do {
            $result = $this->client->getRecords([
                'Limit' => $this->numberOfRecordsPerBatch,
                'ShardIterator' => $shardIterator
            ]);

            $recordsInBatch = count($result->get('Records'));
            $recordsInShard = $recordsInShard+$recordsInBatch;
            $shardIterator = $result->get('NextShardIterator');
            $emptyMillisBehindLatestSuccessivelyDurationCount = $result->get('MillisBehindLatest') === 0 ? $emptyMillisBehindLatestSuccessivelyDurationCount+1 : 0;

            usleep(200 * 1000);
        } while ($emptyMillisBehindLatestSuccessivelyDurationCount < self::MAX_EMPTY_MILLISBEHINDLATEST_SUCCESSIVELY_DURATIONS);
        
        return $recordsInShard;
    }

    /**
     * @param string $streamName
     * @param array|string $data
     * @return \Aws\Result
     */
    public function putRecords($streamName, $data)
    {
        $parameter = [ 'StreamName' => $streamName, 'Records' => []];

        if (!is_array($data)) {
            $data = [$data];
        }
        
        foreach ($data as $item) {
            $parameter['Records'][] = ['Data' => $item, 'PartitionKey' => md5($item)];
        }
        
        $result = $this->client->putRecords($parameter);
        return $result;
    }
}
