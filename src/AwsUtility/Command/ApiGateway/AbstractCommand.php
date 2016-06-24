<?php

namespace AwsUtility\Command\ApiGateway;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

class AbstractCommand extends \AwsUtility\Command\AbstractCommand
{
    /**
     * @var \AwsUtility\Services\ApiGateway
     */
    protected $apiGatewayService = null;
    
    /**
     * @var array
     */
    protected $restApis = [];
    
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
        
        $sdk = new \Aws\Sdk();
        $client = $sdk->createApiGateway(
            [
                'region' => $this->getRegion(), 
                'version' => $this->settings->get('services.api_gateway.version'),
                'credentials' => $this->getCredentials()
            ]
        );
        $this->apiGatewayService = new \AwsUtility\Services\ApiGateway($client);
        
        $this->restApis = $this->apiGatewayService->getRestApis();
        if (empty($this->restApis)) {
            throw new \Exception('No API Gateway Rest APIs available');
        }
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
        $this->interactAskForRestApiName($input, $output);
        $restApiName = $input->getArgument('restApiName');
        $restApi = $this->restApis[$restApiName];
        
        $this->interactAskForResourcePathPart($input, $output, $restApi['id']);
        $resourcePathPart = $input->getArgument('resourcePathPart');

        $this->endpoint = $this->apiGatewayService->getEndpoint($restApi, $resourcePathPart, $this->getRegion());

        $output->writeln('');
        $output->writeln('Selected rest api id: ' . $restApi['id']);
        $output->writeln('Selected rest api name: ' . $restApiName);
        $output->writeln('Selected resource: ' . $resourcePathPart);
        $output->writeln('Endpoint: ' . $this->endpoint);
        $output->writeln('');
    }
    
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function interactAskForRestApiName(InputInterface $input, OutputInterface $output)
    {
        $restApiName = $input->getArgument('restApiName');
        if (!empty($restApiName)) {
            if (!array_key_exists($restApiName, $this->restApis)) {
                throw new \Exception('RestAPI "' . $restApiName . '" not available');
            }
            return;
        }
        
        $restApisForQuestion = [];
        foreach ($this->restApis as $api) {
            $restApisForQuestion[] = $api['name'];
        }
        
        $helper = $this->getHelper('question');
        $question = new ChoiceQuestion('Please select a RestAPI', $restApisForQuestion);
        $question->setErrorMessage('RestAPI %s is invalid.');

        $restApiName = $helper->ask($input, $output, $question);
        $input->setArgument('restApiName', $restApiName);
    }
    
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param string $restApiId
     */
    protected function interactAskForResourcePathPart(InputInterface $input, OutputInterface $output, $restApiId)
    {
        $resources = $this->apiGatewayService->getResources($restApiId);
        if (empty($resources)) {
            throw new \Exception('No API Gateway resources available for RestAPI: ' . $restApiId);
        }
        
        $resourcePathPart = $input->getArgument('resourcePathPart');
        if (!empty($resourcePathPart)) {
            if (!array_key_exists($resourcePathPart, $resources)) {
                throw new \Exception('Resource path part "' . $resourcePathPart . '" not available');
            }
            return;
        }

        $resourcesForQuestion = [];
        foreach ($resources as $resource) {
            $resourcesForQuestion[] = $resource['pathPart'];
        }

        $helper = $this->getHelper('question');
        $question = new ChoiceQuestion('Please select a resource', $resourcesForQuestion);
        $question->setErrorMessage('Resource %s is invalid.');

        $resourcePathPart = $helper->ask($input, $output, $question);
        $input->setArgument('resourcePathPart', $resourcePathPart);
    }
}
