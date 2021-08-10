<?php
declare(strict_types=1);

namespace Exbico\Underwriting\Api\V1;

use Exbico\Underwriting\Api\V1\CreditRating\Nbch\CreditRatingNbchInterface;
use Exbico\Underwriting\Api\V1\ReportPrice\ReportPriceInterface;
use Exbico\Underwriting\Api\V1\ReportStatus\ReportStatusInterface;
use Exbico\Underwriting\Api\V1\Scoring\ScoringInterface;

interface ApiFactoryInterface
{
    public function creditRatingNbch(): CreditRatingNbchInterface;
    public function scoring(): ScoringInterface;
    public function reportStatus(): ReportStatusInterface;
    public function reportPrice(): ReportPriceInterface;
}
