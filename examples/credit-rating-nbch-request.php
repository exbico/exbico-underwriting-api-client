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
printf("Credit rating NBCH requested with ID: %d\n", $reportStatus->getRequestId());

/**
 * Credit rating for lead request
 */
$reportStatus = $client->reports()->creditRatingNbch()->requestLeadReport(5276642, $document);
printf("Lead credit rating NBCH requested with ID: %d\n", $reportStatus->getRequestId());