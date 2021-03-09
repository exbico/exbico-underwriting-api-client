<?php
declare(strict_types=1);

namespace Exbico\Underwriting\Api\V1\ReportStatus;

use Exbico\Underwriting\Dto\V1\Response\ReportStatusDto;

interface ReportStatusInterface
{
    /**
     * Getting status and links to ordered report
     * @param int $requestId
     * @return mixed
     */
    public function getReportStatus(int $requestId): ReportStatusDto;
}