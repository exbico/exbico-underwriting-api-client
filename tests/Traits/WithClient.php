<?php
declare(strict_types=1);

namespace Exbico\Tests\Traits;

use Exbico\ApiSettings;
use Exbico\Client;
use LogicException;

trait WithClient
{
    protected $client;

    public function setUp(): void
    {
        $apiToken = getenv('API_TOKEN');
        $apiUrl = getenv('API_URL');
        if (empty($apiToken) || empty($apiUrl)) {
            throw new LogicException('Exbico API token or API URL not provided');
        }
        $apiSettings = new ApiSettings($apiToken);
        $apiSettings->setBaseUrl($apiUrl);
        $this->client = new Client($apiSettings);
    }

    public function getClient(): Client
    {
        return $this->client;
    }
}