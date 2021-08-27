<?php

use Exbico\Underwriting\Dto\V1\Request\ReportPriceRequestDto;

require_once __DIR__ . '/bootstrap/bootstrap.php';

/*******************************/
/**** GET REPORT PRICE SCORING ****/
/*******************************/

$client = getTestClient();
$reportPriceDto = new ReportPriceRequestDto();
$reportPriceDto->setReportType('scoring');
$reportPrice = $client->reports()->reportPrice()->getReportPrice($reportPriceDto);
printf('Report price: %s' . PHP_EOL, $reportPrice->getPrice());
