<?php
declare(strict_types=1);

namespace Exbico\Underwriting\Api\V1;

use Exbico\Underwriting\Exception\NotEnoughMoneyException;
use Exbico\Underwriting\Exception\ReportGettingErrorException;
use Exbico\Underwriting\Exception\ReportNotReadyException;
use Exbico\Underwriting\Exception\ResponseParsingException;
use Psr\Http\Message\ResponseInterface;

abstract class ReportApi extends Api
{
    private const MESSAGE_NOT_ENOUGH_MONEY =
        'An error has occurred. Please check you have enough money to get this report.';
    private const MESSAGE_REPORT_GETTING_ERROR = 'Report getting error';

    /**
     * @param ResponseInterface $response
     * @throws ReportNotReadyException
     */
    private function checkForReportNotReady(ResponseInterface $response): void
    {
        if ($response->getStatusCode() === ReportNotReadyException::HTTP_STATUS) {
            throw new ReportNotReadyException('Report not ready yet');
        }
    }

    /**
     * @param ResponseInterface $response
     * @throws NotEnoughMoneyException
     * @throws ResponseParsingException
     */
    private function checkNotEnoughMoney(ResponseInterface $response): void
    {
        if ($response->getStatusCode() === NotEnoughMoneyException::HTTP_STATUS) {
            $result = $this->parseResponseResult($response);
            if (isset($result['message']) && $result['message'] === self::MESSAGE_NOT_ENOUGH_MONEY) {
                throw new NotEnoughMoneyException($result['message']);
            }
        }
    }

    /**
     * @param ResponseInterface $response
     * @throws ReportGettingErrorException
     * @throws ResponseParsingException
     */
    private function checkReportGettingError(ResponseInterface $response): void
    {
        if ($response->getStatusCode() === ReportGettingErrorException::HTTP_STATUS) {
            $result = $this->parseResponseResult($response);
            if (isset($result['message']) && $result['message'] === self::MESSAGE_REPORT_GETTING_ERROR) {
                throw new ReportGettingErrorException($result['message']);
            }
        }
    }
}
