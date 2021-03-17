<?php

namespace Exbico\Underwriting\Tests\Dto\V1\Response;

use Exbico\Underwriting\Dto\AbstractDto;
use Exbico\Underwriting\Dto\V1\Response\ReportStatusDto;
use Exception;
use PHPUnit\Framework\TestCase;

class ReportStatusDtoTest extends TestCase
{

    /**
     * @throws Exception
     */
    public function testCreateWithoutConstructor(): void
    {
        $testRequestStatus = 'inProgress';
        $testRequestId = random_int(1, 99999999);
        $reportStatus = new ReportStatusDto();
        $reportStatus->setStatus($testRequestStatus);
        $reportStatus->setRequestId($testRequestId);
        self::assertInstanceOf(AbstractDto::class, $reportStatus);
        self::assertEquals($testRequestStatus, $reportStatus->getStatus());
        self::assertEquals($testRequestId, $reportStatus->getRequestId());
    }

    /**
     * @throws Exception
     */
    public function testCreateViaConstructor(): void
    {
        $testRequestStatus = 'inProgress';
        $testRequestId = random_int(1, 99999999);
        $reportStatus = new ReportStatusDto([
            'status' => $testRequestStatus,
            'requestId' => $testRequestId,
        ]);
        self::assertInstanceOf(AbstractDto::class, $reportStatus);
        self::assertEquals($testRequestStatus, $reportStatus->getStatus());
        self::assertEquals($testRequestId, $reportStatus->getRequestId());
    }
}
