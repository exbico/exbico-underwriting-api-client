<?php
declare(strict_types=1);
require_once __DIR__ . '/../vendor/autoload.php';

use Exbico\Underwriting\ApiSettings;
use Exbico\Underwriting\Client;

function getClient(): Client
{
    $apiSettings = new ApiSettings(getenv('API_TOKEN'));
    $apiSettings->setBaseUrl(getenv('API_URL'));
    return new Client($apiSettings);
}