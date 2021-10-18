<?php
declare(strict_types=1);

use Exbico\Underwriting\ApiSettings;
use Exbico\Underwriting\Client;
use Exbico\Underwriting\Dto\V1\Request\DocumentWithIssueDateDto;
use Exbico\Underwriting\Dto\V1\Request\IncomeDto;
use Exbico\Underwriting\Dto\V1\Request\PersonWithBirthDateDto;
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

function getTestDocumentWithIssueDate(): DocumentWithIssueDateDto
{
    $document = new DocumentWithIssueDateDto();
    $document->setNumber('333333');
    $document->setSeries('5555');
    $document->setIssueDate('2020-10-20');
    return $document;
}

function getTestPersonWithBirthDate(): PersonWithBirthDateDto
{
    $person = new PersonWithBirthDateDto();
    $person->setFirstname('Иван');
    $person->setLastname('Иванов');
    $person->setPatronymic('Иванович');
    $person->setBirthDate('2000-12-12');
    return $person;
}

function getTestIncome(): IncomeDto
{
    $income = new IncomeDto();
    $income->setMonthlyIncome(50000);
    return $income;
}
