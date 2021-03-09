<?php
declare(strict_types=1);

namespace Exbico\Api\V1;

use Exbico\Api\V1\CreditRating\Nbch\CreditRatingNbch;
use Exbico\Api\V1\CreditRating\Nbch\CreditRatingNbchInterface;
use Exbico\Api\V1\ReportStatus\ReportStatus;
use Exbico\Api\V1\ReportStatus\ReportStatusInterface;
use Exbico\Client;

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

    public function reportStatus(): ReportStatusInterface
    {
        return new ReportStatus($this->client);
    }
}