<?php

namespace Exbico\Underwriting\Tests\Dto\V1\Request;

use Exbico\Underwriting\Dto\AbstractDto;
use Exbico\Underwriting\Dto\V1\Request\IncomeDto;
use Exception;
use PHPUnit\Framework\TestCase;

class IncomeDtoTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testCreateWithoutConstructor(): void
    {
        $testMonthlyIncome = random_int(1, 10000000);
        $income = new IncomeDto();
        $income->setMonthlyIncome($testMonthlyIncome);
        self::assertInstanceOf(AbstractDto::class,$income);
        self::assertEquals($testMonthlyIncome, $income->getMonthlyIncome());
    }

    /**
     * @throws Exception
     */
    public function testCreateViaConstructor(): void
    {
        $testMonthlyIncome = random_int(1, 10000000);
        $income = new IncomeDto([
            'monthlyIncome' => $testMonthlyIncome,
        ]);
        self::assertInstanceOf(AbstractDto::class, $income);
        self::assertEquals($testMonthlyIncome, $income->getMonthlyIncome());
    }
}
