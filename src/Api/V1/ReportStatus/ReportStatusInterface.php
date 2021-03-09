<?php
declare(strict_types=1);

namespace Exbico\Underwriting\Api\V1\ReportStatus;

interface ReportStatusInterface
{
    /**
     * Getting status and links to ordered report
     * @param int $requestId
     * @return mixed
     */
    public function getReportStatus(int $requestId);
}