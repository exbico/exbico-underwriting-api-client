<?php
require_once __DIR__ . '/bootstrap/bootstrap.php';

/***************************/
/**** GET REPORT STATUS ****/
/***************************/

$client = getTestClient();
$requestId = $argv[1] ?? null;
if (is_null($requestId)) {
    throw new InvalidArgumentException("Request ID not provided");
}
$reportStatus = $client->api()->reportStatus()->getReportStatus($requestId);
printf("Report status: %s\n", $reportStatus->getStatus());