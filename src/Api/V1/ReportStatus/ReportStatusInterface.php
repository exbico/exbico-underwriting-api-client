<?php
declare(strict_types=1);

namespace Exbico\Underwriting\Api\V1\ReportStatus;

use Exbico\Underwriting\Dto\V1\Response\ReportStatusDto;
use Exbico\Underwriting\Exception\BadRequestException;
use Exbico\Underwriting\Exception\ForbiddenException;
use Exbico\Underwriting\Exception\HttpException;
use Exbico\Underwriting\Exception\NotFoundException;
use Exbico\Underwriting\Exception\ServerErrorException;
use Exbico\Underwriting\Exception\TooManyRequestsException;
use Exbico\Underwriting\Exception\UnauthorizedException;
use Psr\Http\Client\ClientExceptionInterface;
use RuntimeException;

interface ReportStatusInterface
{
    /**
     * Get report status by requestId
     * @param int $requestId
     * @return ReportStatusDto
     * @throws BadRequestException
     * @throws ForbiddenException
     * @throws HttpException
     * @throws NotFoundException
     * @throws ServerErrorException
     * @throws TooManyRequestsException
     * @throws UnauthorizedException
     * @throws ClientExceptionInterface
     * @throws RuntimeException
     */
    public function getReportStatus(int $requestId): ReportStatusDto;
}
