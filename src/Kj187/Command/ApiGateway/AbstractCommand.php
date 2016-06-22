<?php

namespace Kj187\Command\ApiGateway;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

class AbstractCommand extends \Kj187\Command\AbstractCommand
{
    const SERVICE_NAME = 'execute-api';
    
    /**
     * @var \Aws\ApiGateway\ApiGatewayClient
     */
    protected $client = null;
    
    /**
     * @var array
     */
    protected $restApis = [];
    
    /**
     * @var array
     */
    protected $resources = [];
    
    /**
     * @var string
     */
    protected $endpoint = '';
    
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */    
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);
        $this->initializeRestApis();
    }
    
    protected function configure()
    {
        $this
            ->addArgument(
                'restApiName',
                InputArgument::REQUIRED,
                'The API\'s name.'
            )
            ->addArgument(
                'resourcePathPart',
                InputArgument::REQUIRED,
                'The last path segment for this resource.'
            );
            parent::configure();
    }
    
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $this->interactAskForRestApi($input, $output);
        $restApiName = $input->getArgument('restApiName');
        $stageName = $this->getStageName($restApiName);
        $restApiId = $this->restApis[$restApiName]['id'];
        
        $this->interactAskForResource($input, $output, $restApiId);
        $resourcePathPart = $input->getArgument('resourcePathPart');
        
        $this->buildEndpoint($restApiId, $stageName, $resourcePathPart);
        
        $output->writeln('');
        $output->writeln('Selected rest api: ' . $stageName . ' (' . $restApiName . ')');
        $output->writeln('Selected resource: ' . $resourcePathPart);
        $output->writeln('Endpoint: ' . $this->endpoint);
        $output->writeln('');
    }
    
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function interactAskForRestApi(InputInterface $input, OutputInterface $output)
    {
        $restApiName = $input->getArgument('restApiName');
        if (!empty($restApiName)) {
            if (!array_key_exists($restApiName, $this->restApis)) {
                throw new \Exception('RestAPI "' . $restApiName . '" not available');
            }
            return;
        }
        
        $restApis = [];
        foreach ($this->restApis as $api) {
            $restApis[] = $api['name'];
        }
        
        $helper = $this->getHelper('question');
        $question = new ChoiceQuestion('Please select a RestAPI', $restApis);
        $question->setErrorMessage('RestAPI %s is invalid.');

        $restApiName = $helper->ask($input, $output, $question);
        $input->setArgument('restApiName', $restApiName);
    }
    
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param string $restApiId
     */
    protected function interactAskForResource(InputInterface $input, OutputInterface $output, $restApiId)
    {
        $this->initializeResources($restApiId);
        $resourcePathPart = $input->getArgument('resourcePathPart');
        if (!empty($resourcePathPart)) {
            if (!array_key_exists($resourcePathPart, $this->resources)) {
                throw new \Exception('Resource path part "' . $resourcePathPart . '" not available');
            }
            return;
        }

        $resources = [];
        foreach ($this->resources as $resource) {
            $resources[] = $resource['pathPart'];
        }

        $helper = $this->getHelper('question');
        $question = new ChoiceQuestion('Please select a resource', $resources);
        $question->setErrorMessage('Resource %s is invalid.');

        $resourcePathPart = $helper->ask($input, $output, $question);
        $input->setArgument('resourcePathPart', $resourcePathPart);
    }

    /**
     * @throws \Exception
     */
    protected function initializeRestApis()
    {
        $client = $this->getClient();
        $restApis = $client->getRestApis([]);
        
        if (empty($restApis['items'])) {
            throw new \Exception('No API Gateway Rest APIs available');
        }
        
        foreach ($restApis['items'] as $api) {
            $this->restApis[$api['name']] = $api;
        }
    }
    
    /**
     * @param string $restApiId
     * @throws \Exception
     */
    protected function initializeResources($restApiId)
    {
        $client = $this->getClient();
        $resources = $client->getResources(['restApiId' => $restApiId]);
        
        if (empty($resources['items'])) {
            throw new \Exception('No API Gateway resources available for RestAPI: ' . $restApiId);
        }

        foreach ($resources['items'] as $resource) {
            if (!isset($resource['pathPart'])) {
                continue;
            }
            $this->resources[$resource['pathPart']] = $resource;
        }
    }
    
    /**
     * @param string $stageName
     * @return string
     */
    protected function getStageName($stageName)
    {
        $stageName = str_replace('-', '_', $stageName);
        $stageName = $stageName . '_stage';
        return $stageName;
    }    
    
    /**
     * @param string $restApiId
     * @param string $restApiName
     * @param string $resourcePathPart
     */
    protected function buildEndpoint($restApiId, $stageName, $resourcePathPart)
    {
        $service = self::SERVICE_NAME;
        $region = $this->getRegion();
        $this->endpoint = "https://{$restApiId}.{$service}.{$region}.amazonaws.com/{$stageName}/{$resourcePathPart}";
    }
    
    /**
     * @return string
     */
    protected function getEndpoint()
    {
        return $this->endpoint;
    }

    /**
     * @return \Aws\ApiGateway\ApiGatewayClient
     */
    protected function getClient()
    {
        if ($this->client === null) {
            $sdk = new \Aws\Sdk();
            $this->client = $sdk->createApiGateway(
                [
                    'region' => $this->getRegion(), 
                    'version' => $this->_settings->get('services.api_gateway.version'),
                    'credentials' => $this->getCredentials()
                ]
            );
        }

        return $this->client;
    }
}
