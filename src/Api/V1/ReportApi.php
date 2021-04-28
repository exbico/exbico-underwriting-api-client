<?php
declare(strict_types=1);

namespace Exbico\Underwriting\Api\V1;

use Exbico\Underwriting\Exception\NotEnoughMoneyException;
use Exbico\Underwriting\Exception\ReportNotReadyException;
use JsonException;
use Psr\Http\Message\ResponseInterface;

abstract class ReportApi extends Api
{
    private const MESSAGE_NOT_ENOUGH_MONEY = 'An error has occurred. Please check you have enough money to get this report.';

    /**
     * @param ResponseInterface $response
     * @throws JsonException
     */
    protected function checkForErrors(ResponseInterface $response): void
    {
        $this->checkForReportNotReady($response);
        $this->checkNotEnoughMoney($response);
        parent::checkForErrors($response);
    }

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
     * @throws JsonException
     * @throws NotEnoughMoneyException
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
}