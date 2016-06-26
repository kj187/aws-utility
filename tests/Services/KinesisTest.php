<?php

namespace AwsUtility\Tests;

class KinesisTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function findAllStreamNamesReturnsExpectedStreamNames()
    {
        $kinesisClient = $this->getMockBuilder('\Aws\Kinesis\KinesisClient')
            ->disableOriginalConstructor()
            ->setMethods(['listStreams'])
            ->getMock();

        $kinesisClient->method('listStreams')->willReturn(new \Aws\Result(['StreamNames' => ['Steam 1', 'Stream 2', 'Stream 3']]));

        $kinesisService = new \AwsUtility\Services\Kinesis($kinesisClient);
        $streams = $kinesisService->findAllStreamNames();

        $this->assertSame(3, count($streams));
        $this->assertSame('Stream 2', $streams[1]);
    }

    /**
     * @test
     */
    public function findAllStreamNamesThrowsExceptionWithNoResult()
    {
        $kinesisClient = $this->getMockBuilder('\Aws\Kinesis\KinesisClient')
            ->disableOriginalConstructor()
            ->setMethods(['listStreams'])
            ->getMock();

        $kinesisClient->method('listStreams')->willReturn(new \Aws\Result([]));

        try {
            $kinesisService = new \AwsUtility\Services\Kinesis($kinesisClient);
            $streams = $kinesisService->findAllStreamNames();
        } catch (\Exception $e) {
            $this->assertSame('No Kinesis streams available', $e->getMessage());
        }
    }

    /**
     * @test
     * @dataProvider providerPutRecordsReturnsExpectedResult
     */
    public function putRecordsReturnsExpectedResult($streamName, $data)
    {
        $kinesisClient = $this->getMockBuilder('\Aws\Kinesis\KinesisClient')
            ->disableOriginalConstructor()
            ->setMethods(['putRecords'])
            ->getMock();

        $kinesisClient->method('putRecords')->willReturn(new \Aws\Result(
            [
                'FailedRecordCount' => 0,
                'Records' => [
                    'SequenceNumber' => '49562949323907278595484582225718660021271390437964251138',
                    'ShardId' => 'shardId-000000000000'
                ]
            ]
        ));

        $kinesisService = new \AwsUtility\Services\Kinesis($kinesisClient);
        $result = $kinesisService->putRecords($streamName, $data);

        $this->assertSame(0, $result->get('FailedRecordCount'));
        $this->assertSame('49562949323907278595484582225718660021271390437964251138', $result->get('Records')['SequenceNumber']);
        $this->assertSame('shardId-000000000000', $result->get('Records')['ShardId']);
    }

    public function providerPutRecordsReturnsExpectedResult()
    {
        return [
            ['StreamName 1', ['Name' => 'Julian Kleinhans']],
            ['StreamName 1', 'Julian Kleinhans']
        ];
    }
    
    /**
     * @test
     */
    public function findAllShardIdsReturnsExpectedArrayOfShards()
    {
        $kinesisClient = $this->getMockBuilder('\Aws\Kinesis\KinesisClient')
            ->disableOriginalConstructor()
            ->setMethods(['describeStream'])
            ->getMock();
        
        $kinesisClient->method('describeStream')->willReturn(new \Aws\Result(
            [
                'StreamDescription' => [
                    'Shards' => [
                        ['ShardId' => 'shardId-000000000000'],
                        ['ShardId' => 'shardId-000000000001']
                    ]
                ]
            ]
        ));
        
        $kinesisService = new \AwsUtility\Services\Kinesis($kinesisClient);
        $shardIds = $kinesisService->findAllShardIds('TestStream');
        
        $this->assertSame(2, count($shardIds));
        $this->assertSame('shardId-000000000001', $shardIds[1]);
    }
    
    /**
     * @test
     * @dataProvider providerGetShardRecordCountReturnsExpectedCount
     */
    public function getShardRecordCountReturnsExpectedCount($records, $expectedCount)
    {
        $kinesisClient = $this->getMockBuilder('\Aws\Kinesis\KinesisClient')
            ->disableOriginalConstructor()
            ->setMethods(['getShardIterator', 'getRecords'])
            ->getMock();
        
        $kinesisClient->method('getShardIterator')->willReturn(new \Aws\Result(
            ['ShardIterator' => 'AAAAAAAAAAGxb6ax9mpfnXP2Ib7BumGxWftdCvJclv9px']
        ));
        
        $kinesisClient->method('getRecords')->willReturn(new \Aws\Result(
            [
                'Records' => $records,
                'NextShardIterator' => 'AAAAAAAAAAGxb6ax9mpfnXP2Ib7BumGxWftdCvJclv9py',
                'MillisBehindLatest' => 0
            ]
        ));        
        
        $kinesisService = new \AwsUtility\Services\Kinesis($kinesisClient);
        $recordCount = $kinesisService->getShardRecordCount('shardId-000000000000', 'TestStream');

        $this->assertSame($expectedCount, $recordCount);
    } 
    
    public function providerGetShardRecordCountReturnsExpectedCount()
    {
        return [
            [[['1']], 1],
            [[['1'], ['2']], 2],
            [[['1'], ['2'], [3]], 3],
            [[['1'], ['2'], [3], [4]], 4],
            [[['1'], ['2'], [3], [4], [5]], 5]
        ];
    }
}
