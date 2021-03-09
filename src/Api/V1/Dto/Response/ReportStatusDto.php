<?php
namespace Exbico\Underwriting\Api\V1\Dto\Response;

use Exbico\Underwriting\Api\AbstractDto;

class ReportStatusDto extends AbstractDto
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