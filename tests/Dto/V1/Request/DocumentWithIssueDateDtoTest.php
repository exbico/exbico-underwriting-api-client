<?php

namespace Exbico\Underwriting\Tests\Dto\V1\Request;

use Exbico\Underwriting\Dto\AbstractDto;
use Exbico\Underwriting\Dto\V1\Request\DocumentWithIssueDateDto;
use Exception;
use PHPUnit\Framework\TestCase;

class DocumentWithIssueDateDtoTest extends TestCase
{
    private const EXAMPLE_ISSUE_DATE = '1970-06-06';

    /**
     * @throws Exception
     */
    public function testCreateWithoutConstructor(): void
    {
        $testSeries = (string)random_int(1000, 9999);
        $testNumber = (string)random_int(100000, 999999);
        $document = new DocumentWithIssueDateDto();
        $document->setSeries($testSeries);
        $document->setNumber($testNumber);
        $document->setIssueDate(self::EXAMPLE_ISSUE_DATE);
        self::assertInstanceOf(AbstractDto::class, $document);
        self::assertEquals($testSeries, $document->getSeries());
        self::assertEquals($testNumber, $document->getNumber());
        self::assertEquals(self::EXAMPLE_ISSUE_DATE, $document->getIssueDate());
    }

    /**
     * @throws Exception
     */
    public function testCreateViaConstructor(): void
    {
        $testSeries = (string)random_int(1000, 9999);
        $testNumber = (string)random_int(100000, 999999);
        $document = new DocumentWithIssueDateDto(
            [
                'series' => $testSeries,
                'number' => $testNumber,
                'issueDate' => self::EXAMPLE_ISSUE_DATE,
            ]
        );
        self::assertInstanceOf(AbstractDto::class, $document);
        self::assertEquals($testSeries, $document->getSeries());
        self::assertEquals($testNumber, $document->getNumber());
        self::assertEquals(self::EXAMPLE_ISSUE_DATE, $document->getIssueDate());
    }
}

