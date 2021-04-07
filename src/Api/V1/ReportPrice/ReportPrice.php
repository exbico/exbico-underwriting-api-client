<?php
declare(strict_types=1);
namespace Exbico\Underwriting\Api\V1\ReportPrice;

use Exbico\Underwriting\Api\V1\Api;
use Exbico\Underwriting\Dto\V1\Request\ReportPriceRequestDto;
use JsonException;
use Psr\Http\Client\ClientExceptionInterface;

class ReportPrice extends Api
{
    /**
     * @param ReportPriceRequestDto $reportPriceDto
     * @throws JsonException
     * @throws ClientExceptionInterface
     */
    public function getReportPrice(ReportPriceRequestDto $reportPriceDto)
    {
        $requestBody = $this->prepareRequestBody($reportPriceDto->toArray());
        $request = $this->makeRequest('POST', 'report-price')
            ->withBody($requestBody);
        $response = $this->sendRequest($request);
        $responseResult = $this->parseResponseResult($response);
    }
}