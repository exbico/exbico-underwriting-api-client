<?php
declare(strict_types=1);

namespace Exbico\Underwriting\Tests\Api\V1\Scoring;

use Exbico\Underwriting\Api\V1\Scoring\Scoring;
use Exbico\Underwriting\Exception\ReportNotReadyException;
use Exbico\Underwriting\Tests\Traits\WithClient;
use Exbico\Underwriting\Tests\Traits\WithResponses;
use Exception;
use JsonException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientExceptionInterface;

class ScoringTest extends TestCase
{
    use WithClient;
    use WithResponses;

    /**
     * @throws ClientExceptionInterface
     * @throws JsonException
     * @throws Exception
     */
    public function testRequestLeadReport(): void
    {
        $requestId = random_int(1, 9999999);
        $leadId = random_int(1, 9999999);
        $client = $this->getClientWithMockHandler([
            $this->getRequestReportSuccessfulResponse($requestId),
        ]);

        $scoring = new Scoring($client);
        $reportStatus = $scoring->requestLeadReport($leadId);
        self::assertEquals($requestId, $reportStatus->getRequestId());
        self::assertEquals('inProgress', $reportStatus->getStatus());
    }

    /**
     * @throws Exception
     * @throws ClientExceptionInterface
     */
    public function testDownloadReport(): void
    {
        $bytes = random_bytes(16384);
        $client = $this->getClientWithMockHandler([
            $this->getDownloadReportSuccessfulResponse($bytes),
            $this->getReportNotReadyYetResponse()
        ]);
        $scoring = new Scoring($client);
        $tempFilename = tempnam(sys_get_temp_dir(), 'pdf');
        $scoring->downloadPdfReport(1, $tempFilename);
        self::assertEquals($bytes, file_get_contents($tempFilename));
        unlink($tempFilename);
        // Report not ready
        $this->expectException(ReportNotReadyException::class);
        $scoring->downloadPdfReport(1, 'test.pdf');
    }
}
