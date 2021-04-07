<?php

namespace Exbico\Underwriting\Api\V1\ReportPrice;

use Exbico\Underwriting\Dto\V1\Request\ReportPriceRequestDto;
use Exbico\Underwriting\Dto\V1\Response\ReportPriceResponseDto;
use Exbico\Underwriting\Exception\ForbiddenException;
use Exbico\Underwriting\Exception\RequestValidationFailedException;
use Exbico\Underwriting\Exception\ServerErrorException;
use Exbico\Underwriting\Exception\TooManyRequestsException;
use Exbico\Underwriting\Exception\UnauthorizedException;

interface ReportPriceInterface
{
    /**
     * @param ReportPriceRequestDto $reportPriceDto
     * @return ReportPriceResponseDto
     * @throws RequestValidationFailedException
     * @throws UnauthorizedException
     * @throws ForbiddenException
     * @throws TooManyRequestsException
     * @throws ServerErrorException
     */
    public function getReportPrice(ReportPriceRequestDto $reportPriceDto): ReportPriceResponseDto;
}