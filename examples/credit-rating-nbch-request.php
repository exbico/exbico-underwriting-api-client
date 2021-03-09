<?php
require_once __DIR__ . '/bootstrap/bootstrap.php';

/************************************/
/**** REQUEST NBCH CREDIT RATING ****/
/************************************/

$client = getTestClient();
$person = getTestPerson();
$document = getTestDocument();

$reportStatus = $client->api()->creditRatingNbch()->requestReport($person, $document);
printf("Credit rating NBCH requested with ID: %d\n", $reportStatus->getRequestId());