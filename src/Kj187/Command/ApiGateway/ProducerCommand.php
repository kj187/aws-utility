<?php

namespace Kj187\Command\ApiGateway;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


class ProducerCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('api-gateway:producer')
            ->setDescription('Push a POST request to a specific API Gateway endpoint');
        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('');
        
        // TODO assumeRole with specific role
        
        // TODO
        $awsKey = (!empty(getenv('AWS_ACCESS_KEY_ID')) ? getenv('AWS_ACCESS_KEY_ID') : 'TODO');
        $awsSecret = (!empty(getenv('AWS_SECRET_ACCESS_KEY')) ? getenv('AWS_SECRET_ACCESS_KEY') : 'TODO');
        $credentials = new \Aws\Credentials\Credentials($awsKey, $awsSecret);

        // TODO
        $body = '[{"TEST_FROM_API":"OK", "targetId":"1099995168","target":"PRODUCT","operation":"UPDATE","sourceId":"INTEGRATIONTEST","auditTrailId":1112749034,"timestamp":"2016-06-16T07:43:23.733Z","changes":[{"targetItemId":"1101731426","targetItem":"SKU_AVAILABILITY","operation":"UPDATE"}]}]';

        $request = new \GuzzleHttp\Psr7\Request('POST', $this->getEndpoint(), [], $body);
    
        $signature = new \Aws\Signature\SignatureV4('execute-api', $this->getRegion());
        $request = $signature->signRequest($request, $credentials);

        $client = new \GuzzleHttp\Client();
        //$response = $client->send($request, []);
        
        print_r($request);
        
        // TODO status code condition
    }
}
