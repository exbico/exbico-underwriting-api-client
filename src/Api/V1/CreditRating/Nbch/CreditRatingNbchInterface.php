<?php

declare(strict_types=1);

namespace Exbico\Underwriting\Api\V1\CreditRating\Nbch;

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

interface CreditRatingNbchInterface
{
    /**
     * Ordering credit rating NBCH product
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
    ): ReportStatusDto;

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
    ): ReportStatusDto;

    /**
     * Getting pdf report of credit rating NBCH product
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
    public function downloadPdfReport(int $requestId, string $savePath): void;
}
