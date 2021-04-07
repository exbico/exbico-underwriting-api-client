<?php
declare(strict_types=1);

namespace Exbico\Underwriting\Dto\V1\Request;

use Exbico\Underwriting\Dto\AbstractDto;

final class ReportPriceDto extends AbstractDto
{
    protected $reportType;
    protected $leadId;

    public function getReportType(): ?string
    {
        return $this->reportType;
    }

    public function setReportType(string $reportType): void
    {
        $this->reportType = $reportType;
    }

    public function getLeadId(): ?int
    {
        return $this->leadId;
    }

    public function setLeadId(int $leadId): void
    {
        $this->leadId = $leadId;
    }
}