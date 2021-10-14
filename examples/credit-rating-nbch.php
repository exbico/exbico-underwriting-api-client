<?php
require_once __DIR__ . '/bootstrap/bootstrap.php';

/*****************************************/
/**** FULL NBCH CREDIT RATING PROCESS ****/
/*****************************************/

$client = getTestClient();
$person = getTestPersonWithBirthDate();
$document = getTestDocumentWithIssueDate();
$income = getTestIncome();
$reportSavePath = __DIR__ . DIRECTORY_SEPARATOR . 'report_' .  date('YmdHis') . '.pdf';

$reportStatus = $client->reports()->creditRatingNbch()->requestReport($person, $document, $income);
printf('Credit rating NBCH requested with ID: %d' . PHP_EOL, $reportStatus->getRequestId());
printf('Waiting for status change' . PHP_EOL);
while ($reportStatus->getStatus() === 'inProgress') {
    sleep(5);
    $reportStatus = $client->reports()->reportStatus()->getReportStatus($reportStatus->getRequestId());
    echo '.';
}
echo PHP_EOL;

if ($reportStatus->getStatus() === 'success') {
    printf('Start to download report' . PHP_EOL);
    $client->reports()->creditRatingNbch()->downloadPdfReport($reportStatus->getRequestId(), $reportSavePath);
    printf('Report downloaded: %s' . PHP_EOL, $reportSavePath);
} else {
    printf('Unable to get report, requestId: %d' . PHP_EOL, $reportStatus->getRequestId());
    printf('Status: %d' . PHP_EOL, $reportStatus->getStatus());
}
