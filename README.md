#Exbico API Underwriting PHP Client

Библиотека для работы с Exbico Underwriting API.

## Использование

### Инициализация клиента
```php
use Exbico\Underwriting\Client;
use Exbico\Underwriting\ApiSettings;

$apiSettings = new ApiSettings('EBC_API_TOKEN');
$client = new Client($apiSettings);
```
### Запрос кредитной истории НБКИ
```php
use Exbico\Underwriting\Dto\V1\Request\DocumentDto;
use Exbico\Underwriting\Dto\V1\Request\PersonDto;

// Паспортные данные
$document = new DocumentDto();
$document->setNumber('333222');
$document->setSeries('6500');

// ФИО
$person = new PersonDto();
$person->setFirstname('Иван');
$person->setLastname('Иванов');
$person->setMiddlename('Иванович');

$reportStatus = $client->reports()->creditRatingNbch()->requestReport($person, $document);
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
printf("PDF Credit Rating NBCH report downloaded: %s", $filename);
```
