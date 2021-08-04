<?php
declare(strict_types=1);

namespace Exbico\Underwriting\Tests\Api\V1\Scoring;

use Exbico\Underwriting\Api\V1\Scoring\Scoring;
use Exbico\Underwriting\Exception\ForbiddenException;
use Exbico\Underwriting\Exception\LeadNotDistributedToContractException;
use Exbico\Underwriting\Exception\NotEnoughMoneyException;
use Exbico\Underwriting\Exception\ReportGettingErrorException;
use Exbico\Underwriting\Exception\ReportNotReadyException;
use Exbico\Underwriting\Tests\Traits\WithClient;
use Exbico\Underwriting\Tests\Traits\WithResponses;
use PHPUnit\Framework\TestCase;

class ScoringTest extends TestCase
{
    use WithClient;
    use WithResponses;

    private const MESSAGE_SCORING_FOR_LEAD_ALREADY_RECEIVED
        = 'Free scoring for the lead %d has already been received.';

    public function testRequestLeadReport(): void
    {
        $requestId = random_int(1, 9999999);
        $leadId = random_int(1, 9999999);
        $scoring = new Scoring($this->getClientWithMockHandler([
            $this->getRequestReportSuccessfulResponse($requestId),
        ]));
        $reportStatus = $scoring->requestLeadReport($leadId);
        self::assertEquals($requestId, $reportStatus->getRequestId());
        self::assertEquals('inProgress', $reportStatus->getStatus());
    }

    public function testRequestLeadReportWhenLeadNotDistributedToContract(): void
    {
        $leadId = random_int(1, 9999999);
        $scoring = new Scoring($this->getClientWithMockHandler([
            $this->getLeadNotDistributedToContractResponse(),
        ]));
        $this->expectException(LeadNotDistributedToContractException::class);
        $scoring->requestLeadReport($leadId);
    }

    public function testRequestLeadReportWhenNotEnoughMoney(): void
    {
        $leadId = random_int(1, 9999999);
        $scoring = new Scoring($this->getClientWithMockHandler([
            $this->getNotEnoughMoneyResponse(),
        ]));
        $this->expectException(NotEnoughMoneyException::class);
        $this->expectExceptionMessage('An error has occurred. Please check you have enough money to get this report.');
        $scoring->requestLeadReport($leadId);
    }

    public function testRequestLeadReportWhenFreeScoringHasBeenAlreadyReceived(): void
    {
        $leadId = random_int(1, 9999999);
        $expectedMessage = sprintf(self::MESSAGE_SCORING_FOR_LEAD_ALREADY_RECEIVED, $leadId);
        $scoring = new Scoring($this->getClientWithMockHandler([
            $this->getForbiddenResponse($expectedMessage),
        ]));
        $this->expectException(ForbiddenException::class);
        $this->expectExceptionMessage($expectedMessage);
        $scoring->requestLeadReport($leadId);
    }

    public function testDownloadReport(): void
    {
        $bytes = random_bytes(16384);
        $scoring = new Scoring($this->getClientWithMockHandler([
            $this->getDownloadReportSuccessfulResponse($bytes),
        ]));
        $tempFilename = tempnam(sys_get_temp_dir(), 'pdf');
        $scoring->downloadPdfReport(1, $tempFilename);
        self::assertEquals($bytes, file_get_contents($tempFilename));
        unlink($tempFilename);
    }

    public function testDownloadReportWhenReportNotReadyYet(): void
    {
        $scoring = new Scoring($this->getClientWithMockHandler([
            $this->getReportNotReadyYetResponse(),
        ]));
        $this->expectException(ReportNotReadyException::class);
        $scoring->downloadPdfReport(1, 'test.pdf');
    }

    public function testDownloadReportWhenGettingError(): void
    {
        $scoring = new Scoring($this->getClientWithMockHandler([
            $this->getReportGettingErrorResponse(),
        ]));
        $this->expectException(ReportGettingErrorException::class);
        $scoring->downloadPdfReport(-1, 'test.pdf');
    }
}
