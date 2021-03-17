<?php

namespace Exbico\Underwriting\Tests\Dto\V1\Request;

use Exbico\Underwriting\Dto\AbstractDto;
use Exbico\Underwriting\Dto\V1\Request\DocumentDto;
use Exception;
use PHPUnit\Framework\TestCase;

class DocumentDtoTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testCreateWithoutConstructor(): void
    {
        $testSeries = (string)random_int(1000, 9999);
        $testNumber = (string)random_int(100000, 999999);
        $document = new DocumentDto();
        $document->setSeries($testSeries);
        $document->setNumber($testNumber);
        self::assertInstanceOf(AbstractDto::class, $document);
        self::assertEquals($document->getSeries(), $testSeries);
        self::assertEquals($document->getNumber(), $testNumber);
    }

    /**
     * @throws Exception
     */
    public function testCreateViaConstructor(): void
    {
        $testSeries = (string)random_int(1000, 9999);
        $testNumber = (string)random_int(100000, 999999);
        $document = new DocumentDto([
            'series' => $testSeries,
            'number' => $testNumber,
        ]);
        self::assertInstanceOf(AbstractDto::class, $document);
        self::assertEquals($testSeries, $document->getSeries());
        self::assertEquals($testNumber, $document->getNumber());
    }
}
