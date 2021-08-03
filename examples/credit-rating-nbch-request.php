<?php
require_once __DIR__ . '/bootstrap/bootstrap.php';

/************************************/
/**** REQUEST NBCH CREDIT RATING ****/
/************************************/

$client = getTestClient();
$person = getTestPerson();
$document = getTestDocument();
$leadId = isset($argv[1]) ? (int)$argv[1] : null;
if ($leadId === null) {
    throw new InvalidArgumentException('Lead ID not provided');
}
/**
 * Person credit rating request
 */
$reportStatus = $client->reports()->creditRatingNbch()->requestReport($person, $document);
printf('Credit rating NBCH requested with ID: %d' . PHP_EOL, $reportStatus->getRequestId());

/**
 * Credit rating for lead request
 */
$reportStatus = $client->reports()->creditRatingNbch()->requestLeadReport($leadId, $document);
printf('Lead credit rating NBCH requested with ID: %d' . PHP_EOL, $reportStatus->getRequestId());
