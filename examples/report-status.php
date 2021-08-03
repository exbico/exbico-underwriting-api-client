<?php
require_once __DIR__ . '/bootstrap/bootstrap.php';

/***************************/
/**** GET REPORT STATUS ****/
/***************************/

$client = getTestClient();
$requestId = $argv[1] ?? null;
if ($requestId === null) {
    throw new InvalidArgumentException('Request ID not provided');
}
$reportStatus = $client->reports()->reportStatus()->getReportStatus($requestId);
printf('Report status: %s' . PHP_EOL, $reportStatus->getStatus());
