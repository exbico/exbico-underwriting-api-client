<?php
declare(strict_types=1);

namespace Exbico\Underwriting\Api\V1\ReportPrice;

use Exbico\Underwriting\Api\V1\Api;
use Exbico\Underwriting\Dto\V1\Request\ReportPriceRequestDto;
use Exbico\Underwriting\Dto\V1\Response\ReportPriceResponseDto;
use Exbico\Underwriting\Exception\ForbiddenException;
use Exbico\Underwriting\Exception\RequestValidationFailedException;
use Exbico\Underwriting\Exception\ServerErrorException;
use Exbico\Underwriting\Exception\TooManyRequestsException;
use Exbico\Underwriting\Exception\UnauthorizedException;
use JsonException;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\StreamInterface;

class ReportPrice extends Api implements ReportPriceInterface
{
    /**
     * @param ReportPriceRequestDto $reportPriceDto
     * @return ReportPriceResponseDto
     * @throws RequestValidationFailedException
     * @throws UnauthorizedException
     * @throws ForbiddenException
     * @throws TooManyRequestsException
     * @throws ServerErrorException
     * @throws JsonException
     * @throws ClientExceptionInterface
     */
    public function getReportPrice(ReportPriceRequestDto $reportPriceDto): ReportPriceResponseDto
    {
        $requestBody = $this->prepareRequestBody($reportPriceDto->toArray());
        $request = $this->makeRequest('POST', 'report-price')
            ->withBody($requestBody);
        $response = $this->sendRequest($request);
        $responseResult = $this->parseResponseResult($response);
        return new ReportPriceResponseDto($responseResult);
    }

    protected function prepareRequestBody(array $body): StreamInterface
    {
        if (array_key_exists('leadId', $body) && $body['leadId'] === null) {
            unset($body['leadId']);
        }
        return parent::prepareRequestBody($body);
    }
}