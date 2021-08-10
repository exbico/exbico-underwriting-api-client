<?php

namespace Exbico\Underwriting\Tests\Api\V1\ReportStatus;

use Exbico\Underwriting\Api\V1\ReportStatus\ReportStatus;
use Exbico\Underwriting\Exception\TooManyRequestsException;
use Exbico\Underwriting\Exception\UnauthorizedException;
use Exbico\Underwriting\Tests\Traits\WithClient;
use Exbico\Underwriting\Tests\Traits\WithResponses;
use Exception;
use GuzzleHttp\Psr7\Response;
use JsonException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\ResponseInterface;

class ReportStatusTest extends TestCase
{
    use WithClient;
    use WithResponses;

    /**
     * @throws Exception
     * @throws ClientExceptionInterface
     */
    public function testGetReportStatus(): void
    {
        $successStatus = 'success';
        $requestId = random_int(1, 9999999);
        $client = $this->getClientWithMockHandler([
            $this->getReportStatusResponse($successStatus, $requestId),
            $this->getUnauthorizedResponse(),
            $this->getTooManyRequestsResponse(),
        ]);
        $reportStatusApi = new ReportStatus($client);
        $reportStatus = $reportStatusApi->getReportStatus($requestId);
        self::assertEquals($successStatus, $reportStatus->getStatus());
        self::assertEquals($requestId, $reportStatus->getRequestId());
        // Unauthorized
        $this->expectException(UnauthorizedException::class);
        $reportStatusApi->getReportStatus($requestId);
        // Too many requests
        $this->expectException(TooManyRequestsException::class);
        $reportStatusApi->getReportStatus($requestId);
    }

    /**
     * @param string $status
     * @param int $requestId
     * @return Response
     * @throws JsonException
     */
    private function getReportStatusResponse(string $status, int $requestId): ResponseInterface
    {
        return new Response(200, [], json_encode(compact(
            'requestId',
            'status'
        ), JSON_THROW_ON_ERROR));
    }
}
