<?php
declare(strict_types=1);

namespace Exbico;

class ApiSettings
{
    private const API_BASE_URL = 'https://app.exbico.ru';
    private const API_BASE_PATH = 'underwritingApi';

    private $baseUrl;
    private $basePath;
    private $token;

    public function __construct(string $token)
    {
        $this->token = $token;
        $this->baseUrl = self::API_BASE_URL;
        $this->basePath = self::API_BASE_PATH;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    public function setBaseUrl(string $baseUrl): void
    {
        $this->baseUrl = $baseUrl;
    }

    public function getApiBasePath(): string
    {
        return $this->basePath;
    }

    public function setApiBasePath(string $basePath): void
    {
        $this->basePath = $basePath;
    }
}