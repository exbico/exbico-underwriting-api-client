<?php
require_once __DIR__ . '/bootstrap/bootstrap.php';

/*********************************************/
/**** REQUEST NBCH CREDIT RATING FOR LEAD ****/
/*********************************************/

$client = getTestClient();
$document = getTestDocument();
$leadId = isset($argv[1]) ? (int)$argv[1] : null;
if ($leadId === null) {
    throw new InvalidArgumentException('Lead ID not provided');
}

/**
 * Credit rating for lead request
 */
$reportStatus = $client->reports()->creditRatingNbch()->requestLeadReport($leadId, $document);
printf('Lead credit rating NBCH requested with ID: %d' . PHP_EOL, $reportStatus->getRequestId());
