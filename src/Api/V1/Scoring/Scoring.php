<?php
declare(strict_types=1);

namespace Exbico\Underwriting\Api\V1\Scoring;

use Exbico\Underwriting\Api\V1\ReportApi;
use Exbico\Underwriting\Dto\V1\Response\ReportStatusDto;
use Exbico\Underwriting\Exception\BadRequestException;
use Exbico\Underwriting\Exception\ForbiddenException;
use Exbico\Underwriting\Exception\HttpException;
use Exbico\Underwriting\Exception\NotFoundException;
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
     * @param int $leadId
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
    public function requestLeadReport(int $leadId): ReportStatusDto
    {
        $requestBody = $this->prepareRequestBody([
            'leadId' => $leadId
        ]);
        $request = $this->makeRequest('POST', 'lead-score')->withBody($requestBody);
        $response = $this->sendRequest($request);
        $responseResult = $this->parseResponseResult($response);
        return new ReportStatusDto($responseResult);
    }

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
     * @throws RuntimeException
     */
    public function downloadPdfReport(int $requestId, string $savePath): void
    {
        $path = sprintf('lead-scoring/%d/pdf', $requestId);
        $request = $this->makeRequest('GET', $path);
        $response = $this->sendRequest($request);
        $this->download($response, $savePath);
    }
}
