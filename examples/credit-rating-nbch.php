<?php
require_once __DIR__ . '/bootstrap.php';

use Exbico\Underwriting\Api\V1\Dto\Request\DocumentDto;
use Exbico\Underwriting\Api\V1\Dto\Request\PersonDto;

$client = getClient();

$document = new DocumentDto();
$document->setNumber('333333');
$document->setSeries('5555');

$person = new PersonDto();
$person->setFirstname('Иван');
$person->setLastname('Иванов');
$person->setMiddlename('Иванович');

$reportStatus = $client->api()->creditRatingNbch()->requestReport($person, $document);
printf("Credit rating NBCH requested with ID: %d\n", $reportStatus->getRequestId());
printf("Waiting for status change\n");
while ($reportStatus->getStatus() === 'inProgress') {
    sleep(5);
    $reportStatus = $client->api()->reportStatus()->getReportStatus($reportStatus->getRequestId());
}

if($reportStatus->getStatus() === 'success') {
    printf("Start to download report\n");
    $reportFilename = './reports/' . date('YmdHis') . '.pdf';
    $client->api()->creditRatingNbch()->getPdfReport($reportStatus->getRequestId(), $reportFilename);
    printf("Report downloaded: %s\n", $reportFilename);
} else {
    var_dump($reportStatus);
}