<?php
declare(strict_types=1);

namespace Exbico\Underwriting\Api\V1\ReportStatus;

use Exbico\Underwriting\Api\V1\Api;
use Exbico\Underwriting\Dto\V1\Response\ReportStatusDto;

class ReportStatus extends Api implements ReportStatusInterface
{
    public function getReportStatus(int $requestId)
    {
        $path = sprintf('report-status/%d', $requestId);
        $request = $this->makeRequest('GET', $path);
        $response = $this->sendRequest($request);
        $responseResult = $this->parseResponseResult($response);
        return new ReportStatusDto($responseResult);
    }
}