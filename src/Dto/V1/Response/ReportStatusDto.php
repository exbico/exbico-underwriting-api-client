<?php
declare(strict_types=1);

namespace Exbico\Underwriting\Dto\V1\Response;

use Exbico\Underwriting\Dto\AbstractDto;

final class ReportStatusDto extends AbstractDto
{
    public $requestId;
    public $status;

    public function getRequestId(): ?int
    {
        return $this->requestId;
    }

    public function setRequestId(int $requestId): void
    {
        $this->requestId = $requestId;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }
}