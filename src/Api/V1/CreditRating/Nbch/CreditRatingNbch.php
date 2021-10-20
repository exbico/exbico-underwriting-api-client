<?php

declare(strict_types=1);

namespace Exbico\Underwriting\Api\V1\CreditRating\Nbch;

use Exbico\Underwriting\Api\V1\ReportApi;
use Exbico\Underwriting\Dto\V1\Request\DocumentWithIssueDateDto;
use Exbico\Underwriting\Dto\V1\Request\IncomeDto;
use Exbico\Underwriting\Dto\V1\Request\PersonWithBirthDateDto;
use Exbico\Underwriting\Dto\V1\Response\ReportStatusDto;
use Exbico\Underwriting\Exception\BadRequestException;
use Exbico\Underwriting\Exception\ForbiddenException;
use Exbico\Underwriting\Exception\HttpException;
use Exbico\Underwriting\Exception\LeadNotDistributedToContractException;
use Exbico\Underwriting\Exception\NotEnoughMoneyException;
use Exbico\Underwriting\Exception\NotFoundException;
use Exbico\Underwriting\Exception\ProductNotAvailableException;
use Exbico\Underwriting\Exception\ReportGettingErrorException;
use Exbico\Underwriting\Exception\ReportNotReadyException;
use Exbico\Underwriting\Exception\RequestPreparationException;
use Exbico\Underwriting\Exception\ResponseParsingException;
use Exbico\Underwriting\Exception\ServerErrorException;
use Exbico\Underwriting\Exception\TooManyRequestsException;
use Exbico\Underwriting\Exception\UnauthorizedException;
use InvalidArgumentException;
use Psr\Http\Client\ClientExceptionInterface;
use RuntimeException;

class CreditRatingNbch extends ReportApi implements CreditRatingNbchInterface
{
    /**
     * Order NBCH credit rating report
     *
     * @param PersonWithBirthDateDto $person
     * @param DocumentWithIssueDateDto $document
     * @param IncomeDto|null $incomeDto
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
    public function requestReport(
        PersonWithBirthDateDto $person,
        DocumentWithIssueDateDto $document,
        ?IncomeDto $incomeDto = null
    ): ReportStatusDto {
        $body = $this->prepareBodyForRequestReport($person, $document, $incomeDto);
        $requestBody = $this->prepareRequestBody($body);
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

    private function prepareBodyForRequestReport(
        PersonWithBirthDateDto $person,
        DocumentWithIssueDateDto $document,
        ?IncomeDto $incomeDto
    ): array {
        $body = [
            'person'   => $person->toArray(),
            'document' => $document->toArray(),
        ];
        if ($incomeDto !== null) {
            $body['income'] = $incomeDto->toArray();
        }
        return $body;
    }

    /**
     * @param int $leadId
     * @param DocumentWithIssueDateDto $document
     * @param IncomeDto|null $incomeDto
     * @return ReportStatusDto
     * @throws ClientExceptionInterface
     * @throws NotEnoughMoneyException
     * @throws LeadNotDistributedToContractException
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
    public function requestLeadReport(
        int $leadId,
        DocumentWithIssueDateDto $document,
        ?IncomeDto $incomeDto = null
    ): ReportStatusDto {
        $body = $this->prepareBodyForRequestLeadReport($leadId, $document, $incomeDto);
        $requestBody = $this->prepareRequestBody($body);
        $request = $this->makeRequest('POST', 'lead-credit-rating-nbch')->withBody($requestBody);
        try {
            $response = $this->sendRequest($request);
        } catch (HttpException $exception) {
            $this->checkForLeadNotDistributedToContract($exception);
            $this->checkNotEnoughMoney($exception);
            $this->checkProductIsAvailable($exception);
            throw $exception;
        }
        $responseResult = $this->parseResponseResult($response);
        return new ReportStatusDto($responseResult);
    }

    private function prepareBodyForRequestLeadReport(
        int $leadId,
        DocumentWithIssueDateDto $document,
        ?IncomeDto $incomeDto
    ): array {
        $body = [
            'leadId'   => $leadId,
            'document' => $document->toArray(),
        ];
        if ($incomeDto !== null) {
            $body['income'] = $incomeDto->toArray();
        }
        return $body;
    }

    /**
     * Download and save NBCH PDF credit rating report
     *
     * @param int $requestId
     * @param string $savePath
     * @throws ReportGettingErrorException
     * @throws ReportNotReadyException
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
        try {
            $response = $this->sendRequest($request);
        } catch (HttpException $exception) {
            $this->checkForReportNotReady($exception);
            $this->checkReportGettingError($exception);
            throw $exception;
        }
        $this->download($response, $savePath);
    }
}
