<?php
declare(strict_types=1);

namespace Exbico\Api\V1\CreditRating\Nbch;

use Exbico\Api\V1\Dto\Request\DocumentDto;
use Exbico\Api\V1\Dto\Request\PersonDto;
use Exbico\Api\V1\Dto\Response\ReportStatusDto;

interface CreditRatingNbchInterface
{
    /**
     * Ordering credit rating NBCH product
     * @param PersonDto $person
     * @param DocumentDto $document
     * @return mixed
     */
    public function requestReport(PersonDto $person, DocumentDto $document): ReportStatusDto;

    /**
     * Getting pdf report of credit rating NBCH product
     * @param int $requestId
     * @param string $savePath
     * @return mixed
     */
    public function getPdfReport(int $requestId, string $savePath);
}