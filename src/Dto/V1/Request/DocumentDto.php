<?php
declare(strict_types=1);

namespace Exbico\Underwriting\Dto\V1\Request;

use Exbico\Underwriting\Dto\AbstractDto;

class DocumentDto extends AbstractDto
{
    protected $series;
    protected $number;

    public function getSeries(): ?string
    {
        return $this->series;
    }

    public function setSeries(string $series): void
    {
        $this->series = $series;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(string $number): void
    {
        $this->number = $number;
    }
}