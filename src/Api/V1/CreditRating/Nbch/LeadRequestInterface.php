<?php

namespace Exbico\Underwriting\Api\V1\CreditRating\Nbch;

use Exbico\Underwriting\Dto\V1\Request\DocumentDto;
use Exbico\Underwriting\Dto\V1\Response\ReportStatusDto;
use Exbico\Underwriting\Exception\ForbiddenException;
use Exbico\Underwriting\Exception\RequestValidationFailedException;
use Exbico\Underwriting\Exception\ServerErrorException;
use Exbico\Underwriting\Exception\TooManyRequestsException;
use Exbico\Underwriting\Exception\UnauthorizedException;

interface LeadRequestInterface
{
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
}