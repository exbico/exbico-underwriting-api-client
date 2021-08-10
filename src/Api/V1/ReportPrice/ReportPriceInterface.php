<?php

namespace Exbico\Underwriting\Api\V1\ReportPrice;

use Exbico\Underwriting\Dto\V1\Request\ReportPriceRequestDto;
use Exbico\Underwriting\Dto\V1\Response\ReportPriceResponseDto;
use Exbico\Underwriting\Exception\ForbiddenException;
use Exbico\Underwriting\Exception\BadRequestException;
use Exbico\Underwriting\Exception\HttpException;
use Exbico\Underwriting\Exception\NotFoundException;
use Exbico\Underwriting\Exception\RequestPreparationException;
use Exbico\Underwriting\Exception\ResponseParsingException;
use Exbico\Underwriting\Exception\ServerErrorException;
use Exbico\Underwriting\Exception\TooManyRequestsException;
use Exbico\Underwriting\Exception\UnauthorizedException;
use InvalidArgumentException;
use Psr\Http\Client\ClientExceptionInterface;

interface ReportPriceInterface
{
    /**
     * @param ReportPriceRequestDto $reportPriceDto
     * @return ReportPriceResponseDto
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
    public function getReportPrice(ReportPriceRequestDto $reportPriceDto): ReportPriceResponseDto;
}
