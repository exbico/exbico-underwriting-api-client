<?php

declare(strict_types=1);

namespace Exbico\Underwriting\Api\V1\Scoring;

use Exbico\Underwriting\Api\V1\ReportApi;
use Exbico\Underwriting\Dto\V1\Request\DocumentWithIssueDateDto;
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
use Exbico\Underwriting\Exception\ServerErrorException;
use Exbico\Underwriting\Exception\TooManyRequestsException;
use Exbico\Underwriting\Exception\UnauthorizedException;
use InvalidArgumentException;
use Psr\Http\Client\ClientExceptionInterface;
use RuntimeException;

class Scoring extends ReportApi implements ScoringInterface
{
    /**
     * @param PersonWithBirthDateDto $person
     * @param ?DocumentWithIssueDateDto $document
     * @return ReportStatusDto
     * @throws NotEnoughMoneyException
     * @throws ProductNotAvailableException
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
    public function requestReport(PersonWithBirthDateDto $person, ?DocumentWithIssueDateDto $document): ReportStatusDto
    {
        $personBody = $person->toArray();
        if (empty($personBody['patronymic'])) {
            unset($personBody['patronymic']);
        }
        $body = [
            'person' => $personBody,
        ];
        if ($document !== null) {
            $body['document'] = $document->toArray();
        }

        $requestBody = $this->prepareRequestBody($body);
        $request = $this->makeRequest('POST', 'scoring')->withBody($requestBody);
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
     * @param ?DocumentWithIssueDateDto $document
     * @return ReportStatusDto
     * @throws NotEnoughMoneyException
     * @throws ProductNotAvailableException
     * @throws LeadNotDistributedToContractException
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
    public function requestLeadReport(int $leadId, ?DocumentWithIssueDateDto $document = null): ReportStatusDto
    {
        $body = compact('leadId');
        if ($document !== null) {
            $body['document'] = $document->toArray();
        }
        $requestBody = $this->prepareRequestBody($body);
        $request = $this->makeRequest('POST', 'lead-scoring')->withBody($requestBody);
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

    /**
     * Download and save scoring report
     *
     * @param int $requestId
     * @param string $savePath
     * @throws ReportNotReadyException
     * @throws ReportGettingErrorException
     * @throws BadRequestException
     * @throws UnauthorizedException
     * @throws ForbiddenException
     * @throws TooManyRequestsException
     * @throws ServerErrorException
     * @throws HttpException
     * @throws ClientExceptionInterface
     * @throws RuntimeException
     */
    public function downloadPdfReport(int $requestId, string $savePath): void
    {
        $path = sprintf('scoring/%d/pdf', $requestId);
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
