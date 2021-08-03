<?php

namespace Exbico\Underwriting\Api\V1\Scoring;

use Exbico\Underwriting\Dto\V1\Response\ReportStatusDto;
use Exbico\Underwriting\Exception\BadRequestException;
use Exbico\Underwriting\Exception\ForbiddenException;
use Exbico\Underwriting\Exception\HttpException;
use Exbico\Underwriting\Exception\ReportNotReadyException;
use Exbico\Underwriting\Exception\ServerErrorException;
use Exbico\Underwriting\Exception\TooManyRequestsException;
use Exbico\Underwriting\Exception\UnauthorizedException;
use JsonException;
use Psr\Http\Client\ClientExceptionInterface;

interface ScoringInterface
{
    /**
     * @param int $leadId
     * @return ReportStatusDto
     * @throws JsonException
     * @throws ClientExceptionInterface
     */
    public function requestLeadReport(int $leadId): ReportStatusDto;

    /**
     * Download and save scoring report
     * @param int $requestId
     * @param string $savePath
     * @throws BadRequestException
     * @throws UnauthorizedException
     * @throws ForbiddenException
     * @throws ReportNotReadyException
     * @throws TooManyRequestsException
     * @throws ServerErrorException
     * @throws HttpException
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    public function downloadPdfReport(int $requestId, string $savePath): void;
}
