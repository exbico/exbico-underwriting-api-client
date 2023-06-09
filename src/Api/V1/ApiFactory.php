<?php
declare(strict_types=1);

namespace Exbico\Underwriting\Api\V1;

use Exbico\Underwriting\Api\V1\CreditRating\Nbch\CreditRatingNbch;
use Exbico\Underwriting\Api\V1\CreditRating\Nbch\CreditRatingNbchInterface;
use Exbico\Underwriting\Api\V1\ReportPrice\ReportPrice;
use Exbico\Underwriting\Api\V1\ReportPrice\ReportPriceInterface;
use Exbico\Underwriting\Api\V1\ReportStatus\ReportStatus;
use Exbico\Underwriting\Api\V1\ReportStatus\ReportStatusInterface;
use Exbico\Underwriting\Api\V1\Scoring\Scoring;
use Exbico\Underwriting\Api\V1\Scoring\ScoringInterface;
use Exbico\Underwriting\Client;

class ApiFactory implements ApiFactoryInterface
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function creditRatingNbch(): CreditRatingNbchInterface
    {
        return new CreditRatingNbch($this->client);
    }

    public function scoring(): ScoringInterface
    {
        return new Scoring($this->client);
    }

    public function reportStatus(): ReportStatusInterface
    {
        return new ReportStatus($this->client);
    }

    public function reportPrice(): ReportPriceInterface
    {
        return new ReportPrice($this->client);
    }
}
