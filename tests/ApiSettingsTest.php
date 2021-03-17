<?php

namespace Exbico\Underwriting\Tests;

use Exbico\Underwriting\ApiSettings;
use PHPUnit\Framework\TestCase;

class ApiSettingsTest extends TestCase
{
    private $apiSettings;
    private const API_TOKEN = 'example_token';

    public function setUp(): void
    {
        $this->apiSettings = new ApiSettings(self::API_TOKEN);
        parent::setUp();
    }

    public function testToken(): void
    {
        self::assertEquals(self::API_TOKEN, $this->apiSettings->getToken());
    }

    public function testBaseUrl(): void
    {
        $apiBaseUrl = 'https://test.app.exbico.ru/underwritingApi';
        $this->apiSettings->setBaseUrl($apiBaseUrl);
        self::assertEquals($apiBaseUrl, $this->apiSettings->getBaseUrl());
    }

    public function testApiBasePath(): void
    {
        $apiBasePath = 'v1';
        $this->apiSettings->setApiBasePath($apiBasePath);
        self::assertEquals($apiBasePath, $this->apiSettings->getApiBasePath());
    }
}
