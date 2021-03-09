<?php
require_once __DIR__ . '/bootstrap/bootstrap.php';

/*****************************************/
/**** FULL NBCH CREDIT RATING PROCESS ****/
/*****************************************/

$client = getTestClient();
$person = getTestPerson();
$document = getTestDocument();
$reportSavePath = __DIR__ . DIRECTORY_SEPARATOR . 'report_' .  date('YmdHis') . '.pdf';

$reportStatus = $client->api()->creditRatingNbch()->requestReport($person, $document);
printf("Credit rating NBCH requested with ID: %d\n", $reportStatus->getRequestId());
printf("Waiting for status change");
while ($reportStatus->getStatus() === 'inProgress') {
    sleep(5);
    $reportStatus = $client->api()->reportStatus()->getReportStatus($reportStatus->getRequestId());
    echo '.';
}
echo "\n";

if($reportStatus->getStatus() === 'success') {
    printf("Start to download report\n");
    $client->api()->creditRatingNbch()->downloadPdfReport($reportStatus->getRequestId(), $reportSavePath);
    printf("Report downloaded: %s\n", $reportSavePath);
} else {
    var_dump($reportStatus);
}