<?php

namespace Exbico\Underwriting\Tests\Api\V1\ReportPrice;

use Exbico\Underwriting\Api\V1\ReportPrice\ReportPrice;
use Exbico\Underwriting\Api\V1\ReportPrice\ReportPriceInterface;
use Exbico\Underwriting\Dto\V1\Request\ReportPriceRequestDto;
use Exbico\Underwriting\Exception\ForbiddenException;
use Exbico\Underwriting\Exception\BadRequestException;
use Exbico\Underwriting\Exception\HttpException;
use Exbico\Underwriting\Exception\LeadNotDistributedToContractException;
use Exbico\Underwriting\Exception\NotFoundException;
use Exbico\Underwriting\Exception\RequestPreparationException;
use Exbico\Underwriting\Exception\ResponseParsingException;
use Exbico\Underwriting\Exception\ServerErrorException;
use Exbico\Underwriting\Exception\TooManyRequestsException;
use Exbico\Underwriting\Exception\UnauthorizedException;
use Exbico\Underwriting\Tests\Traits\WithClient;
use Exbico\Underwriting\Tests\Traits\WithResponses;
use Exception;
use GuzzleHttp\Psr7\Response;
use InvalidArgumentException;
use JsonException;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\ResponseInterface;

class ReportPriceTest extends TestCase
{
    use WithClient;
    use WithResponses;

    private const PRICE_NBCH_REPORT = 100;
    private const PRICE_NBCH_FOR_LEAD = 0;
    private const PRICE_SCORING_FOR_LEAD = 0;
    private const MESSAGE_NON_EXISTENT_REPORT = 'Enum failed, enum: ["credit-rating-nbch","scoring"]';

    /**
     * @var ReportPriceInterface
     */
    private $reportPriceApi;

    /**
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
     * @throws JsonException
     * @throws ExpectationFailedException
     * @throws ClientExceptionInterface
     * @throws Exception
     */
    public function testGetReportPrice(): void
    {
        $client = $this->getClientWithMockHandler([
            $this->getReportPriceSuccessfulResponse(self::PRICE_NBCH_REPORT),
            $this->getReportPriceSuccessfulResponse(self::PRICE_NBCH_FOR_LEAD),
            $this->getReportPriceSuccessfulResponse(self::PRICE_SCORING_FOR_LEAD),
            $this->getBadRequestResponse(self::MESSAGE_NON_EXISTENT_REPORT),
        ]);
        $reportPriceApi = new ReportPrice($client);
        // Credit rating nbch successful
        $reportPriceRequestDto = new ReportPriceRequestDto();
        $reportPriceRequestDto->setReportType('credit-rating-nbch');
        $reportPriceResponseDto = $reportPriceApi->getReportPrice($reportPriceRequestDto);
        self::assertEquals(self::PRICE_NBCH_REPORT, $reportPriceResponseDto->getPrice());
        // Credit rating nbch for lead successful
        $reportPriceRequestDto = new ReportPriceRequestDto();
        $reportPriceRequestDto->setReportType('credit-rating-nbch');
        $reportPriceRequestDto->setLeadId(random_int(1, 100000));
        $reportPriceResponseDto = $reportPriceApi->getReportPrice($reportPriceRequestDto);
        self::assertEquals(self::PRICE_NBCH_FOR_LEAD, $reportPriceResponseDto->getPrice());
        // Scoring for lead successful
        $reportPriceRequestDto = new ReportPriceRequestDto();
        $reportPriceRequestDto->setReportType('scoring');
        $reportPriceRequestDto->setLeadId(random_int(1, 100000));
        $reportPriceResponseDto = $reportPriceApi->getReportPrice($reportPriceRequestDto);
        self::assertEquals(self::PRICE_SCORING_FOR_LEAD, $reportPriceResponseDto->getPrice());
        // Request non-existent report type
        $reportPriceRequestDto = new ReportPriceRequestDto();
        $reportPriceRequestDto->setReportType('non-existent-report-type');
        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage(self::MESSAGE_NON_EXISTENT_REPORT);
        $reportPriceApi->getReportPrice($reportPriceRequestDto);
    }

    public function testGetReportPriceWhenLeadNotDistributedToContract(): void
    {
        $reportPriceApi = new ReportPrice($this->getClientWithMockHandler([
            $this->getLeadNotDistributedToContractResponse()
        ]));
        $reportPriceRequestDto = new ReportPriceRequestDto();
        $reportPriceRequestDto->setReportType('credit-rating-nbch');
        $reportPriceRequestDto->setLeadId(random_int(1, 100000));
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
            'price' => $price,
        ], JSON_THROW_ON_ERROR));
    }
}
