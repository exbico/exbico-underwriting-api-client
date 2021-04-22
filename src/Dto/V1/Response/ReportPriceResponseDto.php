<?php
declare(strict_types=1);

namespace Exbico\Underwriting\Dto\V1\Response;

use Exbico\Underwriting\Dto\AbstractDto;

class ReportPriceResponseDto extends AbstractDto
{
    protected $price;

    public function getPrice(): ?int
    {
        return $this->price;
    }
}