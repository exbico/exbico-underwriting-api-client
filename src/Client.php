<?php
declare(strict_types=1);

namespace Exbico;

use Exbico\Api\V1\ApiFactory;
use Exbico\Api\V1\ApiFactoryInterface;
use GuzzleHttp\Client as GuzzleHttpClient;
use Psr\Http\Client\ClientInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class Client
{
    /**
     * Http Client
     *
     * @var ClientInterface
     */
    protected $httpClient;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var ApiSettings
     */
    protected $apiSettings;

    public function __construct(ApiSettings $apiSettings)
    {
        $this->setApiSettings($apiSettings);
        $this->setHttpClient(new GuzzleHttpClient());
        $this->setLogger(new NullLogger());
    }

    public function api(): ApiFactoryInterface
    {
        return new ApiFactory($this);
    }

    public function getApiSettings(): ApiSettings
    {
        return $this->apiSettings;
    }

    public function setApiSettings(ApiSettings $apiSettings): void
    {
        $this->apiSettings = $apiSettings;
    }

    /**
     * Set the PSR-7 compatible Http Client to make API requests
     *
     * @param ClientInterface $httpClient
     */
    public function setHttpClient(ClientInterface $httpClient): void
    {
        $this->httpClient = $httpClient;
    }

    public function getHttpClient(): ClientInterface
    {
        return $this->httpClient;
    }

    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    /**
     * Set the PSR-3 compatible Logger
     *
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }
}