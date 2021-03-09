<?php
declare(strict_types=1);

namespace Exbico\Underwriting\Api\V1\CreditRating\Nbch;

use Exbico\Underwriting\Api\V1\Api;
use Exbico\Underwriting\Api\V1\Dto\Request\DocumentDto;
use Exbico\Underwriting\Api\V1\Dto\Request\PersonDto;
use Exbico\Underwriting\Api\V1\Dto\Response\ReportStatusDto;

class CreditRatingNbch extends Api implements CreditRatingNbchInterface
{
    /**
     * @param PersonDto $person
     * @param DocumentDto $document
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \Psr\Http\Client\ClientExceptionInterface
     * @throws \JsonException
     */
    public function requestReport(PersonDto $person, DocumentDto $document): ReportStatusDto
    {
        $requestBody = $this->prepareRequestBody([
            'person' => $person->toArray(),
            'document' => $document->toArray(),
        ]);
        $request = $this->makeRequest('POST', 'credit-rating-nbch')->withBody($requestBody);
        $response = $this->sendRequest($request);
        $responseResult = $this->parseResponseResult($response);
        return new ReportStatusDto($responseResult);
    }

    public function getPdfReport(int $requestId, string $savePath): void
    {
        $path = sprintf('credit-rating-nbch/%d/pdf', $requestId);
        $request = $this->makeRequest('GET', $path);
        $response = $this->sendRequest($request);
        $this->download($response, $savePath);
    }
}