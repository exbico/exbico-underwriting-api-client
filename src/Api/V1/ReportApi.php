<?php
declare(strict_types=1);

namespace Exbico\Underwriting\Api\V1;

use Exbico\Underwriting\Exception\HttpException;
use Exbico\Underwriting\Exception\LeadNotDistributedToContractException;
use Exbico\Underwriting\Exception\NotEnoughMoneyException;
use Exbico\Underwriting\Exception\ProductNotAvailableException;
use Exbico\Underwriting\Exception\ReportGettingErrorException;
use Exbico\Underwriting\Exception\ReportNotReadyException;

abstract class ReportApi extends Api
{
    private const MESSAGE_NOT_ENOUGH_MONEY                 =
        'An error has occurred. Please check you have enough money to get this report.';
    private const MESSAGE_REPORT_GETTING_ERROR             = 'Report getting error';
    private const MESSAGE_PRODUCT_NOT_AVAILABLE            = 'Requested product is not available for your account';
    private const PATTERN_LEAD_NOT_DISTRIBUTED_TO_CONTRACT = '/Lead with id \d+ was not distributed to your contract/';

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
     * @param HttpException $exception
     * @throws ReportGettingErrorException
     */
    protected function checkReportGettingError(HttpException $exception): void
    {
        if ($exception->getCode() === ReportGettingErrorException::HTTP_STATUS
            && $exception->getMessage() === self::MESSAGE_REPORT_GETTING_ERROR) {
            throw new ReportGettingErrorException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    /**
     * @param HttpException $exception
     * @throws ReportNotReadyException
     */
    protected function checkForReportNotReady(HttpException $exception): void
    {
        if ($exception->getCode() === ReportNotReadyException::HTTP_STATUS) {
            throw new ReportNotReadyException('Report not ready yet');
        }
    }

    /**
     * @throws LeadNotDistributedToContractException
     */
    protected function checkForLeadNotDistributedToContract(HttpException $exception): void
    {
        if ($exception->getCode() === LeadNotDistributedToContractException::HTTP_STATUS
            && preg_match(self::PATTERN_LEAD_NOT_DISTRIBUTED_TO_CONTRACT, $exception->getMessage())) {
            throw new LeadNotDistributedToContractException($exception->getMessage());
        }
    }
}
