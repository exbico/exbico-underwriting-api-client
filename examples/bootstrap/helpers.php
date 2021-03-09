<?php
declare(strict_types=1);

use Exbico\Underwriting\ApiSettings;
use Exbico\Underwriting\Client;
use Exbico\Underwriting\Dto\V1\Request\DocumentDto;
use Exbico\Underwriting\Dto\V1\Request\PersonDto;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

function getTestClient(): Client
{
    $logger = new Logger('api');
    $logger->pushHandler(new StreamHandler(__DIR__ . '/api.log'));
    $apiSettings = new ApiSettings(getenv('API_TOKEN'));
    $apiSettings->setBaseUrl(getenv('API_URL'));
    $client = new Client($apiSettings);
    $client->setLogger($logger);
    return $client;
}

function getTestDocument(): DocumentDto
{
    $document = new DocumentDto();
    $document->setNumber('333333');
    $document->setSeries('5555');
    return $document;
}

function getTestPerson(): PersonDto
{
    $person = new PersonDto();
    $person->setFirstname('Иван');
    $person->setLastname('Иванов');
    $person->setMiddlename('Иванович');
    return $person;
}

