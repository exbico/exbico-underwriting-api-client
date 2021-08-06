# Exbico API Underwriting PHP Client

Библиотека для работы с Exbico Underwriting API.

## Установка
```
composer require exbico/underwriting-api-client
```

## Использование

### Инициализация клиента
```php
use Exbico\Underwriting\Client;
use Exbico\Underwriting\ApiSettings;

$apiSettings = new ApiSettings('EBC_API_TOKEN');
$client = new Client($apiSettings);
```

### Получение стоимости отчета
```php
use Exbico\Underwriting\Dto\V1\Request\ReportPriceRequestDto;

$reportPriceRequestDto = new ReportPriceRequestDto();
$reportPriceRequestDto->setReportType('credit-rating-nbch');
$reportPriceRequestDto->setLeadId(15);

$reportPriceDto = $client->reports()->reportPrice()->getReportPrice($reportPriceRequestDto);
$reportPrice = $reportPriceDto->getPrice();
```

### Запрос кредитного рейтинга НБКИ по ФИО и паспортным данным
```php
use Exbico\Underwriting\Dto\V1\Request\DocumentDto;
use Exbico\Underwriting\Dto\V1\Request\PersonDto;

// Document data
$document = new DocumentDto();
$document->setNumber('333222');
$document->setSeries('6500');

// Person data
$person = new PersonDto();
$person->setFirstname('Иван');
$person->setLastname('Иванов');
$person->setMiddlename('Иванович');

$reportStatus = $client->reports()->creditRatingNbch()->requestReport($person, $document);
$requestId = $reportStatus->getRequestId(); // 21320130
$statusLabel = $reportStatus->getStatus(); // 'inProgress'
```

### Запрос кредитного рейтинга НБКИ по ID лида и паспортным данным
```php
use Exbico\Underwriting\Dto\V1\Request\DocumentDto;

// Document data
$document = new DocumentDto();
$document->setNumber('333222');
$document->setSeries('6500');

$leadId = 12345; // Exbico Lead Id

$reportStatus = $client->reports()->creditRatingNbch()->requestLeadReport($leadId, $document);
$requestId = $reportStatus->getRequestId(); // 21320130
$statusLabel = $reportStatus->getStatus(); // 'inProgress'
```

### Запрос скоринга по ID лида
```php

$leadId = 12345; // Exbico Lead Id

$reportStatus = $client->reports()->scoring()->requestLeadReport($leadId);
$requestId = $reportStatus->getRequestId(); // 21320130
$statusLabel = $reportStatus->getStatus(); // 'inProgress'
```

### Получение статуса подготовки отчета
```php
$requestId = 21320130;
$reportStatus = $client->reports()->reportStatus()->getReportStatus($requestId);
$statusLabel = $reportStatus->getStatus(); // 'success'
```

### Получение отчета кредитной истории НБКИ
```php
// ... Check status of report is 'success' 
$requestId = 21320130;
$filename = 'report.pdf';
$client->reports()->creditRatingNbch()->downloadPdfReport($requestId, $filename);
```

### Получение отчета по продукту "Скоринг"
```php
// ... Check status of report is 'success' 
$requestId = 21320130;
$filename = 'report.pdf';
$client->reports()->scoring()->downloadPdfReport($requestId, $filename);
```

Примеры использования находятся в папке `examples`.

## Тесты
```
./vendor/bin/phpunit tests
```