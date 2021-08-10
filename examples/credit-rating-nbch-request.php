<?php
require_once __DIR__ . '/bootstrap/bootstrap.php';

/************************************/
/**** REQUEST NBCH CREDIT RATING ****/
/************************************/

$client = getTestClient();
$person = getTestPerson();
$document = getTestDocument();
/**
 * Person credit rating request
 */
$reportStatus = $client->reports()->creditRatingNbch()->requestReport($person, $document);
printf('Credit rating NBCH requested with ID: %d' . PHP_EOL, $reportStatus->getRequestId());