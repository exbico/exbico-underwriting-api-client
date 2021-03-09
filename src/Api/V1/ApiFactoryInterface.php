<?php
declare(strict_types=1);

namespace Exbico\Api\V1;

use Exbico\Api\V1\CreditRating\Nbch\CreditRatingNbchInterface;
use Exbico\Api\V1\ReportStatus\ReportStatusInterface;

interface ApiFactoryInterface
{
    public function creditRatingNbch(): CreditRatingNbchInterface;
    public function reportStatus(): ReportStatusInterface;
}