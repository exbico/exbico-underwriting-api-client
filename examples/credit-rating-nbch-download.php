<?php
require_once __DIR__ . '/bootstrap/bootstrap.php';

/*************************************/
/**** DOWNLOAD NBCH CREDIT RATING ****/
/*************************************/

$client = getTestClient();
$requestId = $argv[1] ?? null;
if ($requestId === null) {
    throw new InvalidArgumentException('Request ID not provided');
}
$reportSavePath = __DIR__ . DIRECTORY_SEPARATOR . 'report_' . date('YmdHis') . '.pdf';

$client->reports()->creditRatingNbch()->downloadPdfReport($requestId, $reportSavePath);
printf('PDF Report downloaded: %s', $reportSavePath);
