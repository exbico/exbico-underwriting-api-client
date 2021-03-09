<?php
declare(strict_types=1);

namespace Exbico\Underwriting\Api\V1\CreditRating\Nbch;

use Exbico\Underwriting\Api\V1\Api;
use Exbico\Underwriting\Dto\V1\Request\DocumentDto;
use Exbico\Underwriting\Dto\V1\Request\PersonDto;
use Exbico\Underwriting\Dto\V1\Response\ReportStatusDto;
use Exbico\Underwriting\Exception\ReportNotReadyException;
use JsonException;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\ResponseInterface;

class CreditRatingNbch extends Api implements CreditRatingNbchInterface
{
    /**
     * Order NBCH credit rating report
     * @param PersonDto $person
     * @param DocumentDto $document
     * @return ReportStatusDto
     * @throws ClientExceptionInterface
     * @throws JsonException
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

    /**
     * Download and save NBCH PDF credit rating report
     * @param int $requestId
     * @param string $savePath
     * @throws ClientExceptionInterface
     */
    public function getPdfReport(int $requestId, string $savePath): void
    {
        $path = sprintf('credit-rating-nbch/%d/pdf', $requestId);
        $request = $this->makeRequest('GET', $path);
        $response = $this->sendRequest($request);
        $this->download($response, $savePath);
    }


    /**
     * @param ResponseInterface $response
     */
    protected function checkForErrors(ResponseInterface $response): void
    {
        if ($response->getStatusCode() === ReportNotReadyException::HTTP_STATUS) {
            throw new ReportNotReadyException($response->getBody()->getContents());
        }
        parent::checkForErrors($response);
    }
}