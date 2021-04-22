<?php

namespace Exbico\Underwriting\Tests\Api\V1;

use Exbico\Underwriting\Api\V1\ApiFactory;
use Exbico\Underwriting\Api\V1\ApiFactoryInterface;
use Exbico\Underwriting\Api\V1\CreditRating\Nbch\CreditRatingNbchInterface;
use Exbico\Underwriting\Api\V1\ReportPrice\ReportPriceInterface;
use Exbico\Underwriting\Api\V1\ReportStatus\ReportStatusInterface;
use Exbico\Underwriting\Tests\Traits\WithClient;
use PHPUnit\Framework\TestCase;

class ApiFactoryTest extends TestCase
{
    use WithClient;

    /**
     * @var ApiFactoryInterface
     */
    private $apiFactory;

    public function setUp(): void
    {
        parent::setUp();
        $this->apiFactory = new ApiFactory($this->getClient());
    }

    public function testInstanceOf(): void
    {
        self::assertInstanceOf(ApiFactoryInterface::class, $this->apiFactory);
    }

    public function testCreditRatingNbch(): void
    {
        $creditRatingNbchApi = $this->apiFactory->creditRatingNbch();
        self::assertInstanceOf(CreditRatingNbchInterface::class, $creditRatingNbchApi);
    }

    public function testReportStatus(): void
    {
        $reportStatusApi = $this->apiFactory->reportStatus();
        self::assertInstanceOf(ReportStatusInterface::class, $reportStatusApi);
    }

    public function testReportPrice(): void
    {
        $reportPriceApi = $this->apiFactory->reportPrice();
        self::assertInstanceOf(ReportPriceInterface::class, $reportPriceApi);
    }
}
