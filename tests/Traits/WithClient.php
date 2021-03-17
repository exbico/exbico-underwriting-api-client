<?php
declare(strict_types=1);

namespace Exbico\Underwriting\Tests\Traits;

use Exbico\Underwriting\ApiSettings;
use Exbico\Underwriting\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Client as GuzzleHttpClient;

trait WithClient
{
    public function getClient(): Client
    {
        $apiSettings = new ApiSettings('some_token');
        return new Client($apiSettings);
    }

    public function getClientWithMockHandler(array $mockHandler): Client
    {
        $mock = new MockHandler($mockHandler);
        $handlerStack = HandlerStack::create($mock);
        $guzzleClient = new GuzzleHttpClient(['handler' => $handlerStack]);
        $client = $this->getClient();
        $client->setHttpClient($guzzleClient);
        return $client;
    }
}