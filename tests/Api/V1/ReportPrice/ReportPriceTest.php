<?php

namespace Exbico\Underwriting\Tests\Api\V1\ReportPrice;

use Exbico\Underwriting\Api\V1\ReportPrice\ReportPrice;
use Exbico\Underwriting\Api\V1\ReportPrice\ReportPriceInterface;
use Exbico\Underwriting\Dto\V1\Request\ReportPriceRequestDto;
use Exbico\Underwriting\Exception\ForbiddenException;
use Exbico\Underwriting\Exception\BadRequestException;
use Exbico\Underwriting\Exception\LeadNotDistributedToContractException;
use Exbico\Underwriting\Exception\TooManyRequestsException;
use Exbico\Underwriting\Exception\UnauthorizedException;
use Exbico\Underwriting\Tests\Traits\WithClient;
use Exbico\Underwriting\Tests\Traits\WithResponses;
use GuzzleHttp\Psr7\Response;
use JsonException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\ResponseInterface;

class ReportPriceTest extends TestCase
{
    use WithClient;
    use WithResponses;

    private const EXAMPLE_REPORT_PRICE = 100;

    /**
     * @var ReportPriceInterface
     */
    private $reportPriceApi;

    /**
     * @throws JsonException
     */
    public function setUp(): void
    {
        parent::setUp();
        $client = $this->getClientWithMockHandler([
            $this->getReportPriceSuccessfulResponse(self::EXAMPLE_REPORT_PRICE),
            $this->getBadRequestResponse('Wrong price format'),
            $this->getUnauthorizedResponse(),
            $this->getForbiddenResponse('Access denied'),
            $this->getTooManyRequestsResponse(),
            $this->getLeadNotDistributedToContractResponse(),
        ]);
        $this->reportPriceApi = new ReportPrice($client);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    public function testGetReportPrice(): void
    {
        $client = $this->getClientWithMockHandler([
            $this->getReportPriceSuccessfulResponse(self::EXAMPLE_REPORT_PRICE),
        ]);
        $reportPriceApi = new ReportPrice($client);
        $reportPriceRequestDto = new ReportPriceRequestDto();
        $reportPriceRequestDto->setReportType('credit-rating-nbch');
        $reportPriceResponseDto = $reportPriceApi->getReportPrice($reportPriceRequestDto);
        self::assertEquals(self::EXAMPLE_REPORT_PRICE, $reportPriceResponseDto->getPrice());
    }

    /**
     * @throws JsonException
     * @throws ClientExceptionInterface
     */
    public function testBadRequest(): void
    {
        $client = $this->getClientWithMockHandler([
            $this->getBadRequestResponse('Wrong price format'),
        ]);
        $reportPriceApi = new ReportPrice($client);
        $reportPriceRequestDto = new ReportPriceRequestDto();
        $reportPriceRequestDto->setReportType('non-existent-report-type');
        $this->expectException(BadRequestException::class);
        $reportPriceApi->getReportPrice($reportPriceRequestDto);
    }

    /**
     * @throws JsonException
     * @throws ClientExceptionInterface
     */
    public function testWhenUnauthorized(): void
    {
        $client = $this->getClientWithMockHandler([
            $this->getUnauthorizedResponse(),
        ]);
        $reportPriceApi = new ReportPrice($client);
        $reportPriceRequestDto = new ReportPriceRequestDto();
        $reportPriceRequestDto->setReportType('credit-rating-nbch');
        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessage('Wrong token');
        $reportPriceApi->getReportPrice($reportPriceRequestDto);
    }

    /**
     * @throws JsonException
     * @throws ClientExceptionInterface
     */
    public function testWhenForbidden(): void
    {
        $client = $this->getClientWithMockHandler([
            $this->getForbiddenResponse('Access denied'),
        ]);
        $reportPriceApi = new ReportPrice($client);
        $reportPriceRequestDto = new ReportPriceRequestDto();
        $reportPriceRequestDto->setReportType('credit-rating-nbch');
        $this->expectException(ForbiddenException::class);
        $this->expectExceptionMessage('Access denied');
        $reportPriceApi->getReportPrice($reportPriceRequestDto);
    }

    /**
     * @throws JsonException
     * @throws ClientExceptionInterface
     */
    public function testWhenTooManyRequests(): void
    {
        $client = $this->getClientWithMockHandler([
            $this->getTooManyRequestsResponse(),
        ]);
        $reportPriceApi = new ReportPrice($client);
        $reportPriceRequestDto = new ReportPriceRequestDto();
        $reportPriceRequestDto->setReportType('credit-rating-nbch');
        $this->expectException(TooManyRequestsException::class);
        $reportPriceApi->getReportPrice($reportPriceRequestDto);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    public function testWhenLeadNotDistributedToContract(): void
    {
        $client = $this->getClientWithMockHandler([
            $this->getLeadNotDistributedToContractResponse(),
        ]);
        $reportPriceApi = new ReportPrice($client);
        $reportPriceRequestDto = new ReportPriceRequestDto();
        $reportPriceRequestDto->setReportType('credit-rating-nbch');
        $this->expectException(LeadNotDistributedToContractException::class);
        $reportPriceApi->getReportPrice($reportPriceRequestDto);
    }

    /**
     * @param int $price
     * @return Response
     * @throws JsonException
     */
    private function getReportPriceSuccessfulResponse(int $price): ResponseInterface
    {
        return new Response(200, [], json_encode([
            "price" => $price,
        ], JSON_THROW_ON_ERROR));
    }
}
