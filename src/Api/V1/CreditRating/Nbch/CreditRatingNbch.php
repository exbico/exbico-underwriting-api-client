<?php
declare(strict_types=1);

namespace Exbico\Underwriting\Api\V1\CreditRating\Nbch;

use Exbico\Underwriting\Api\V1\ReportApi;
use Exbico\Underwriting\Dto\V1\Request\DocumentDto;
use Exbico\Underwriting\Dto\V1\Request\PersonDto;
use Exbico\Underwriting\Dto\V1\Response\ReportStatusDto;
use Exbico\Underwriting\Exception\ForbiddenException;
use Exbico\Underwriting\Exception\HttpException;
use Exbico\Underwriting\Exception\NotFoundException;
use Exbico\Underwriting\Exception\BadRequestException;
use Exbico\Underwriting\Exception\ProductNotAvailableException;
use Exbico\Underwriting\Exception\RequestPreparationException;
use Exbico\Underwriting\Exception\ResponseParsingException;
use Exbico\Underwriting\Exception\ServerErrorException;
use Exbico\Underwriting\Exception\TooManyRequestsException;
use Exbico\Underwriting\Exception\UnauthorizedException;
use InvalidArgumentException;
use Exbico\Underwriting\Exception\NotEnoughMoneyException;
use Psr\Http\Client\ClientExceptionInterface;
use RuntimeException;

class CreditRatingNbch extends ReportApi implements CreditRatingNbchInterface
{
    /**
     * Order NBCH credit rating report
     * @param PersonDto $person
     * @param DocumentDto $document
     * @return ReportStatusDto
     * @throws NotEnoughMoneyException
     * @throws BadRequestException
     * @throws ForbiddenException
     * @throws HttpException
     * @throws NotFoundException
     * @throws ServerErrorException
     * @throws TooManyRequestsException
     * @throws UnauthorizedException
     * @throws InvalidArgumentException
     * @throws ClientExceptionInterface
     * @throws RuntimeException
     */
    public function requestReport(PersonDto $person, DocumentDto $document): ReportStatusDto
    {
        $requestBody = $this->prepareRequestBody([
            'person' => $person->toArray(),
            'document' => $document->toArray(),
        ]);
        $request = $this->makeRequest('POST', 'credit-rating-nbch')->withBody($requestBody);
        try {
            $response = $this->sendRequest($request);
        } catch (HttpException $exception) {
            $this->checkNotEnoughMoney($exception);
            $this->checkProductIsAvailable($exception);
            throw $exception;
        }
        $responseResult = $this->parseResponseResult($response);
        return new ReportStatusDto($responseResult);
    }

    /**
     * @param int $leadId
     * @param DocumentDto $document
     * @return ReportStatusDto
     * @throws ClientExceptionInterface
     * @throws NotEnoughMoneyException
     * @throws ProductNotAvailableException
     * @throws BadRequestException
     * @throws UnauthorizedException
     * @throws ForbiddenException
     * @throws TooManyRequestsException
     * @throws ServerErrorException
     * @throws HttpException
     * @throws ResponseParsingException
     * @throws RequestPreparationException
     * @throws InvalidArgumentException
     */
    public function requestLeadReport(int $leadId, DocumentDto $document): ReportStatusDto
    {
        $requestBody = $this->prepareRequestBody([
            'leadId' => $leadId,
            'document' => $document->toArray(),
        ]);
        $request = $this->makeRequest('POST', 'lead-credit-rating-nbch')->withBody($requestBody);
        try {
            $response = $this->sendRequest($request);
        } catch (HttpException $exception) {
            $this->checkNotEnoughMoney($exception);
            $this->checkProductIsAvailable($exception);
            throw $exception;
        }
        $responseResult = $this->parseResponseResult($response);
        return new ReportStatusDto($responseResult);
    }

    /**
     * Download and save NBCH PDF credit rating report
     * @param int $requestId
     * @param string $savePath
     * @throws BadRequestException
     * @throws ForbiddenException
     * @throws HttpException
     * @throws NotFoundException
     * @throws ServerErrorException
     * @throws TooManyRequestsException
     * @throws UnauthorizedException
     * @throws ClientExceptionInterface
     * @throws ResponseParsingException
     * @throws RequestPreparationException
     */
    public function downloadPdfReport(int $requestId, string $savePath): void
    {
        $path = sprintf('credit-rating-nbch/%d/pdf', $requestId);
        $request = $this->makeRequest('GET', $path);
        $response = $this->sendRequest($request);
        $this->download($response, $savePath);
    }
}
