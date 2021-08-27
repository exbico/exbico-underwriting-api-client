<?php
declare(strict_types=1);

namespace Exbico\Underwriting\Dto\V1\Request;

final class DocumentWithIssueDateDto extends DocumentDto
{
    protected $issueDate;

    public function getIssueDate(): ?string
    {
        return $this->issueDate;
    }

    public function setIssueDate(string $issueDate): void
    {
        $this->issueDate = $issueDate;
    }
}