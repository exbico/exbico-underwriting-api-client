<?php

namespace Exbico\Underwriting\Tests\Dto\V1\Response;

use Exbico\Underwriting\Dto\AbstractDto;
use Exbico\Underwriting\Dto\V1\Response\ReportPriceResponseDto;
use Exception;
use PHPUnit\Framework\TestCase;

class ReportPriceResponseDtoTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testCreate(): void
    {
        $price = random_int(10, 10000);
        $dto = new ReportPriceResponseDto([
            'price' => $price,
        ]);
        self::assertInstanceOf(AbstractDto::class, $dto);
        self::assertEquals($price, $dto->getPrice());
    }
}
