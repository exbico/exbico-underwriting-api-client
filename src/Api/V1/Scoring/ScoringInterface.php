<?php

namespace Exbico\Underwriting\Api\V1\Scoring;

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

interface ScoringInterface
{
    /**
     * @param PersonWithBirthDateDto $person
     * @param ?DocumentWithIssueDateDto $document
     * @return ReportStatusDto
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
    public function requestReport(PersonWithBirthDateDto $person, ?DocumentWithIssueDateDto $document): ReportStatusDto;

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
    public function requestLeadReport(int $leadId, ?DocumentWithIssueDateDto $document = null): ReportStatusDto;

    /**
     * Download and save scoring report
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
    public function downloadPdfReport(int $requestId, string $savePath): void;
}
