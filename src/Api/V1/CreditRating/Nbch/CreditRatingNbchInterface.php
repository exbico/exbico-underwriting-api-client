<?php
declare(strict_types=1);

namespace Exbico\Underwriting\Api\V1\CreditRating\Nbch;

use Exbico\Underwriting\Dto\V1\Request\DocumentDto;
use Exbico\Underwriting\Dto\V1\Request\PersonDto;
use Exbico\Underwriting\Dto\V1\Response\ReportStatusDto;
use Exbico\Underwriting\Exception\ForbiddenException;
use Exbico\Underwriting\Exception\RequestValidationFailedException;
use Exbico\Underwriting\Exception\ServerErrorException;
use Exbico\Underwriting\Exception\TooManyRequestsException;
use Exbico\Underwriting\Exception\UnauthorizedException;

interface CreditRatingNbchInterface
{
    /**
     * Ordering credit rating NBCH product
     * @param PersonDto $person
     * @param DocumentDto $document
     * @throws RequestValidationFailedException
     * @throws UnauthorizedException
     * @throws ForbiddenException
     * @throws TooManyRequestsException
     * @throws ServerErrorException
     * @return ReportStatusDto
     */
    public function requestReport(PersonDto $person, DocumentDto $document): ReportStatusDto;

    /**
     * @param int $leadId
     * @param DocumentDto $document
     * @return ReportStatusDto
     * @throws RequestValidationFailedException
     * @throws UnauthorizedException
     * @throws ForbiddenException
     * @throws TooManyRequestsException
     * @throws ServerErrorException
     */
    public function requestLeadReport(int $leadId, DocumentDto $document): ReportStatusDto;

    /**
     * Getting pdf report of credit rating NBCH product
     * @param int $requestId
     * @param string $savePath
     * @return mixed
     */
    public function downloadPdfReport(int $requestId, string $savePath);
}