<?php

use Exbico\Underwriting\Dto\V1\Request\ReportPriceRequestDto;

require_once __DIR__ . '/bootstrap/bootstrap.php';

/*******************************/
/**** GET REPORT PRICE NBCH ****/
/*******************************/

$client = getTestClient();
$reportPriceDto = new ReportPriceRequestDto();
$reportPriceDto->setReportType('credit-rating-nbch');
$reportPrice = $client->reports()->reportPrice()->getReportPrice($reportPriceDto);
printf('Report price: %s' . PHP_EOL, $reportPrice->getPrice());

$leadId = isset($argv[1]) ? (int)$argv[1] : null;
if ($leadId === null) {
    throw new InvalidArgumentException('Lead ID not provided');
}
$reportPriceWithLeadDto = new ReportPriceRequestDto();
$reportPriceWithLeadDto->setReportType('credit-rating-nbch');
$reportPriceWithLeadDto->setLeadId($leadId);
$reportPrice = $client->reports()->reportPrice()->getReportPrice($reportPriceWithLeadDto);
printf('Report price for lead: %s' . PHP_EOL, $reportPrice->getPrice());
