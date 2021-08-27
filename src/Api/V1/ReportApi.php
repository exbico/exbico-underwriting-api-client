<?php
declare(strict_types=1);

namespace Exbico\Underwriting\Api\V1;

use Exbico\Underwriting\Exception\HttpException;
use Exbico\Underwriting\Exception\NotEnoughMoneyException;
use Exbico\Underwriting\Exception\ProductNotAvailableException;
use Exbico\Underwriting\Exception\ReportGettingErrorException;
use Exbico\Underwriting\Exception\ReportNotReadyException;
use Exbico\Underwriting\Exception\ResponseParsingException;
use Psr\Http\Message\ResponseInterface;

abstract class ReportApi extends Api
{
    private const MESSAGE_NOT_ENOUGH_MONEY      =
        'An error has occurred. Please check you have enough money to get this report.';
    private const MESSAGE_REPORT_GETTING_ERROR  = 'Report getting error';
    private const MESSAGE_PRODUCT_NOT_AVAILABLE = 'Requested product is not available for your account';

    /**
     * @param ResponseInterface $response
     * @throws ReportNotReadyException
     */
    protected function checkForReportNotReady(ResponseInterface $response): void
    {
        if ($response->getStatusCode() === ReportNotReadyException::HTTP_STATUS) {
            throw new ReportNotReadyException('Report not ready yet');
        }
    }

    /**
     * @throws NotEnoughMoneyException
     */
    protected function checkNotEnoughMoney(HttpException $exception): void
    {
        if ($exception->getCode() === NotEnoughMoneyException::HTTP_STATUS
            && $exception->getMessage() === self::MESSAGE_NOT_ENOUGH_MONEY) {
            throw new NotEnoughMoneyException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    /**
     * @throws ProductNotAvailableException
     */
    protected function checkProductIsAvailable(HttpException $exception): void
    {
        if ($exception->getCode() === ProductNotAvailableException::HTTP_STATUS
            && $exception->getMessage() === self::MESSAGE_PRODUCT_NOT_AVAILABLE) {
            throw new ProductNotAvailableException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    /**
     * @param ResponseInterface $response
     * @throws ReportGettingErrorException
     * @throws ResponseParsingException
     */
    protected function checkReportGettingError(ResponseInterface $response): void
    {
        if ($response->getStatusCode() === ReportGettingErrorException::HTTP_STATUS) {
            $result = $this->parseResponseResult($response);
            if (isset($result['message']) && $result['message'] === self::MESSAGE_REPORT_GETTING_ERROR) {
                throw new ReportGettingErrorException($result['message']);
            }
        }
    }
}
