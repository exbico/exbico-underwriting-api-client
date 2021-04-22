<?php

use Exbico\Underwriting\Dto\V1\Request\ReportPriceRequestDto;

require_once __DIR__ . '/bootstrap/bootstrap.php';

/***************************/
/**** GET REPORT PRICE ****/
/***************************/

$client = getTestClient();
$reportPriceDto = new ReportPriceRequestDto();
$reportPriceDto->setReportType('credit-rating-nbch');
$reportPrice = $client->reports()->reportPrice()->getReportPrice($reportPriceDto);
printf("Report price: %s\n", $reportPrice->getPrice());

$reportPriceWithLeadDto = new ReportPriceRequestDto();
$reportPriceWithLeadDto->setReportType('credit-rating-nbch');
$reportPriceWithLeadDto->setLeadId(2000);
$reportPrice = $client->reports()->reportPrice()->getReportPrice($reportPriceDto);
printf("Report price for lead: %s\n", $reportPrice->getPrice());