<?php

namespace Exbico\Underwriting\Tests\Dto\V1\Request;

use Exbico\Underwriting\Dto\AbstractDto;
use Exbico\Underwriting\Dto\V1\Request\ReportPriceRequestDto;
use PHPUnit\Framework\TestCase;

class ReportPriceDtoTest extends TestCase
{
    private const EXAMPLE_REPORT_TYPE = 'credit-rating-nbch';
    private const EXAMPLE_LEAD_ID = 10;

    public function testCreateWithoutConstructor(): void
    {
        $reportPrice = new ReportPriceRequestDto();
        $reportPrice->setReportType(self::EXAMPLE_REPORT_TYPE);
        $reportPrice->setLeadId(self::EXAMPLE_LEAD_ID);
        self::assertInstanceOf(AbstractDto::class, $reportPrice);
        self::assertEquals(self::EXAMPLE_REPORT_TYPE, $reportPrice->getReportType());
        self::assertEquals(self::EXAMPLE_LEAD_ID, $reportPrice->getLeadId());
    }

    public function testCreateViaConstructor(): void
    {
        $reportPrice = new ReportPriceRequestDto([
            'reportType' => self::EXAMPLE_REPORT_TYPE,
            'leadId' => self::EXAMPLE_LEAD_ID,
        ]);
        self::assertInstanceOf(AbstractDto::class, $reportPrice);
        self::assertEquals(self::EXAMPLE_REPORT_TYPE, $reportPrice->getReportType());
        self::assertEquals(self::EXAMPLE_LEAD_ID, $reportPrice->getLeadId());
    }
}
