<?php

namespace Kj187\Command\ApiGateway;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ProducerCommand extends AbstractCommand
{
    /**
     * @var string
     */
    protected $assumedRoleSessionName = 'aws-utility-api-gateway-producer';
    
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
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $credentials = $this->getCredentials();
        if ($input->hasParameterOption('--assumeRole')) {
            $credentials = $this->getAssumedRoleCredentials();
        }

        $signature = new \Aws\Signature\SignatureV4('execute-api', $this->getRegion());
        $client = new \GuzzleHttp\Client();

        $directoryPath = ROOT_DIR . $this->settings->get('services.api_gateway.producer.mocks.path') . $input->getArgument('restApiName'). '/' . $input->getArgument('resourcePathPart') . '/';
        if (!is_dir($directoryPath)) {
            throw new \Exception('Directory with mocks are not available "' . $directoryPath . '". We expect a subdirectory with the API name and a second subdirectory with the resource name.');
        }
        
		$directoryIterator = new \DirectoryIterator($directoryPath);
        foreach ($directoryIterator as $fileInfo) {
            if ($fileInfo->isDot() || substr($fileInfo->getFilename(), 0, 1) == '.') continue;
            $body = file_get_contents($fileInfo->getPathName());
            $output->writeln('Send mock: ' . $fileInfo->getPathName());
            
            $request = new \GuzzleHttp\Psr7\Request('POST', $this->getEndpoint(), ['X-Source' => 'aws-utility'], $body);
            $request = $signature->signRequest($request, $credentials);
            $response = $client->send($request, []);
        
            $output->writeln('Status Code: ' . $response->getStatusCode());
            $output->writeln('Response body: ' . $response->getBody());
            $output->writeln('---------------------------------------------------------------------------------');
            $output->writeln('');
        }
    }
}
