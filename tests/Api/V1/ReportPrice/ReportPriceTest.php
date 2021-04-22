<?php

namespace Exbico\Underwriting\Tests\Api\V1\ReportPrice;

use Exbico\Underwriting\Api\V1\ReportPrice\ReportPrice;
use Exbico\Underwriting\Api\V1\ReportPrice\ReportPriceInterface;
use Exbico\Underwriting\Dto\V1\Request\ReportPriceRequestDto;
use Exbico\Underwriting\Exception\ForbiddenException;
use Exbico\Underwriting\Exception\RequestValidationFailedException;
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
        ]);
        $this->reportPriceApi = new ReportPrice($client);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    public function testGetReportPrice(): void
    {
        $this->makeCorrectRequest();
        $this->makeBadRequest();
        $this->makeUnauthorizedRequest();
        $this->makeForbiddenRequest();
        $this->makeTooManyRequest();
    }

    /**
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    private function makeCorrectRequest(): void
    {
        $reportPriceRequestDto = new ReportPriceRequestDto();
        $reportPriceRequestDto->setReportType('credit-rating-nbch');
        $reportPriceResponseDto = $this->reportPriceApi->getReportPrice($reportPriceRequestDto);
        self::assertEquals(self::EXAMPLE_REPORT_PRICE, $reportPriceResponseDto->getPrice());
    }

    /**
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    private function makeBadRequest(): void
    {
        $reportPriceRequestDto = new ReportPriceRequestDto();
        $reportPriceRequestDto->setReportType('non-existent-report-type');
        $this->expectException(RequestValidationFailedException::class);
        $this->reportPriceApi->getReportPrice($reportPriceRequestDto);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    private function makeUnauthorizedRequest(): void
    {
        $this->expectException(UnauthorizedException::class);
        $this->makeCorrectRequest();
    }

    /**
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    private function makeForbiddenRequest(): void
    {
        $this->expectException(ForbiddenException::class);
        $this->makeCorrectRequest();
    }

    /**
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    private function makeTooManyRequest(): void
    {
        $this->expectException(TooManyRequestsException::class);
        $this->makeCorrectRequest();
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
