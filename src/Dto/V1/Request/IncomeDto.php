<?php
declare(strict_types=1);

namespace Exbico\Underwriting\Dto\V1\Request;

use Exbico\Underwriting\Dto\AbstractDto;

class IncomeDto extends AbstractDto
{
    protected $monthlyIncome;

    public function getMonthlyIncome(): ?int
    {
        return $this->monthlyIncome;
    }

    public function setMonthlyIncome(int $monthlyIncome): void
    {
        $this->monthlyIncome = $monthlyIncome;
    }
}