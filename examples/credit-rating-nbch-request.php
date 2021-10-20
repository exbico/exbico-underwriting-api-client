<?php
require_once __DIR__ . '/bootstrap/bootstrap.php';

/************************************/
/**** REQUEST NBCH CREDIT RATING ****/
/************************************/

$client = getTestClient();
$person = getTestPersonWithBirthDate();
$document = getTestDocumentWithIssueDate();
$income = getTestIncome();
/**
 * Person credit rating request
 */
$reportStatus = $client->reports()->creditRatingNbch()->requestReport($person, $document, $income);
printf('Credit rating NBCH requested with ID: %d' . PHP_EOL, $reportStatus->getRequestId());
