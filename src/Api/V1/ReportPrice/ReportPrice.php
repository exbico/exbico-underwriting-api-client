<?php
declare(strict_types=1);

namespace Exbico\Underwriting\Api\V1\ReportPrice;

use Exbico\Underwriting\Api\V1\ReportApi;
use Exbico\Underwriting\Dto\V1\Request\ReportPriceRequestDto;
use Exbico\Underwriting\Dto\V1\Response\ReportPriceResponseDto;
use Exbico\Underwriting\Exception\ForbiddenException;
use Exbico\Underwriting\Exception\BadRequestException;
use Exbico\Underwriting\Exception\HttpException;
use Exbico\Underwriting\Exception\LeadNotDistributedToContractException;
use Exbico\Underwriting\Exception\NotFoundException;
use Exbico\Underwriting\Exception\ProductNotAvailableException;
use Exbico\Underwriting\Exception\RequestPreparationException;
use Exbico\Underwriting\Exception\ResponseParsingException;
use Exbico\Underwriting\Exception\ServerErrorException;
use Exbico\Underwriting\Exception\TooManyRequestsException;
use Exbico\Underwriting\Exception\UnauthorizedException;
use InvalidArgumentException;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\StreamInterface;
use function array_key_exists;

class ReportPrice extends ReportApi implements ReportPriceInterface
{
    /**
     * @param ReportPriceRequestDto $reportPriceDto
     * @return ReportPriceResponseDto
     * @throws ProductNotAvailableException
     * @throws LeadNotDistributedToContractException
     * @throws BadRequestException
     * @throws ForbiddenException
     * @throws HttpException
     * @throws NotFoundException
     * @throws RequestPreparationException
     * @throws ResponseParsingException
     * @throws ServerErrorException
     * @throws TooManyRequestsException
     * @throws UnauthorizedException
     * @throws InvalidArgumentException
     * @throws ClientExceptionInterface
     */
    public function getReportPrice(ReportPriceRequestDto $reportPriceDto): ReportPriceResponseDto
    {
        $requestBody = $this->prepareRequestBody($reportPriceDto->toArray());
        $request = $this->makeRequest('POST', 'report-price')
            ->withBody($requestBody);
        try {
            $response = $this->sendRequest($request);
        } catch (HttpException $exception) {
            $this->checkForLeadNotDistributedToContract($exception);
            $this->checkProductIsAvailable($exception);
            throw $exception;
        }
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
