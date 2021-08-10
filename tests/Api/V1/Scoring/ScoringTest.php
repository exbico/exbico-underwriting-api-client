<?php
declare(strict_types=1);

namespace Exbico\Underwriting\Tests\Api\V1\Scoring;

use Exbico\Underwriting\Api\V1\Scoring\Scoring;
use Exbico\Underwriting\Exception\BadRequestException;
use Exbico\Underwriting\Exception\ForbiddenException;
use Exbico\Underwriting\Exception\HttpException;
use Exbico\Underwriting\Exception\LeadNotDistributedToContractException;
use Exbico\Underwriting\Exception\NotEnoughMoneyException;
use Exbico\Underwriting\Exception\NotFoundException;
use Exbico\Underwriting\Exception\ReportGettingErrorException;
use Exbico\Underwriting\Exception\ReportNotReadyException;
use Exbico\Underwriting\Exception\ServerErrorException;
use Exbico\Underwriting\Exception\TooManyRequestsException;
use Exbico\Underwriting\Exception\UnauthorizedException;
use Exbico\Underwriting\Tests\Traits\WithClient;
use Exbico\Underwriting\Tests\Traits\WithResponses;
use Exception;
use InvalidArgumentException;
use JsonException;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientExceptionInterface;
use RuntimeException;

class ScoringTest extends TestCase
{
    use WithClient;
    use WithResponses;

    private const MESSAGE_SCORING_FOR_LEAD_ALREADY_RECEIVED
        = 'Free scoring for the lead %d has already been received.';

    /**
     * @throws BadRequestException
     * @throws RuntimeException
     * @throws ForbiddenException
     * @throws ClientExceptionInterface
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws UnauthorizedException
     * @throws ExpectationFailedException
     * @throws TooManyRequestsException
     * @throws JsonException
     * @throws InvalidArgumentException
     * @throws HttpException
     * @throws ServerErrorException
     * @throws Exception
     */
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

    /**
     * @throws BadRequestException
     * @throws UnauthorizedException
     * @throws TooManyRequestsException
     * @throws JsonException
     * @throws RuntimeException
     * @throws ForbiddenException
     * @throws ClientExceptionInterface
     * @throws InvalidArgumentException
     * @throws HttpException
     * @throws ServerErrorException
     * @throws NotFoundException
     * @throws Exception
     */
    public function testRequestLeadReportWhenLeadNotDistributedToContract(): void
    {
        $leadId = random_int(1, 9999999);
        $scoring = new Scoring($this->getClientWithMockHandler([
            $this->getLeadNotDistributedToContractResponse(),
        ]));
        $this->expectException(LeadNotDistributedToContractException::class);
        $scoring->requestLeadReport($leadId);
    }

    /**
     * @throws BadRequestException
     * @throws ForbiddenException
     * @throws HttpException
     * @throws NotFoundException
     * @throws ServerErrorException
     * @throws TooManyRequestsException
     * @throws UnauthorizedException
     * @throws InvalidArgumentException
     * @throws JsonException
     * @throws ClientExceptionInterface
     * @throws RuntimeException
     * @throws Exception
     */
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

    /**
     * @throws BadRequestException
     * @throws ForbiddenException
     * @throws HttpException
     * @throws NotFoundException
     * @throws ServerErrorException
     * @throws TooManyRequestsException
     * @throws UnauthorizedException
     * @throws InvalidArgumentException
     * @throws JsonException
     * @throws ClientExceptionInterface
     * @throws RuntimeException
     * @throws Exception
     */
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

    /**
     * @throws BadRequestException
     * @throws ForbiddenException
     * @throws HttpException
     * @throws ReportNotReadyException
     * @throws ServerErrorException
     * @throws TooManyRequestsException
     * @throws UnauthorizedException
     * @throws InvalidArgumentException
     * @throws ExpectationFailedException
     * @throws ClientExceptionInterface
     * @throws RuntimeException
     * @throws Exception
     */
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

    /**
     * @throws BadRequestException
     * @throws ForbiddenException
     * @throws HttpException
     * @throws ReportNotReadyException
     * @throws ServerErrorException
     * @throws TooManyRequestsException
     * @throws UnauthorizedException
     * @throws ClientExceptionInterface
     * @throws RuntimeException
     */
    public function testDownloadReportWhenReportNotReadyYet(): void
    {
        $scoring = new Scoring($this->getClientWithMockHandler([
            $this->getReportNotReadyYetResponse(),
        ]));
        $this->expectException(ReportNotReadyException::class);
        $scoring->downloadPdfReport(1, 'test.pdf');
    }

    /**
     * @throws BadRequestException
     * @throws ForbiddenException
     * @throws HttpException
     * @throws ReportNotReadyException
     * @throws ServerErrorException
     * @throws TooManyRequestsException
     * @throws UnauthorizedException
     * @throws JsonException
     * @throws ClientExceptionInterface
     * @throws RuntimeException
     */
    public function testDownloadReportWhenGettingError(): void
    {
        $scoring = new Scoring($this->getClientWithMockHandler([
            $this->getReportGettingErrorResponse(),
        ]));
        $this->expectException(ReportGettingErrorException::class);
        $scoring->downloadPdfReport(-1, 'test.pdf');
    }
}