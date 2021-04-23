<?php

namespace Exbico\Underwriting\Tests\Api\V1\CreditRating\Nbch;

use Exbico\Underwriting\Api\V1\CreditRating\Nbch\CreditRatingNbch;
use Exbico\Underwriting\Dto\V1\Request\DocumentDto;
use Exbico\Underwriting\Dto\V1\Request\PersonDto;
use Exbico\Underwriting\Exception\ForbiddenException;
use Exbico\Underwriting\Exception\ReportNotReadyException;
use Exbico\Underwriting\Exception\RequestValidationFailedException;
use Exbico\Underwriting\Exception\TooManyRequestsException;
use Exbico\Underwriting\Exception\UnauthorizedException;
use Exbico\Underwriting\Tests\Traits\WithClient;
use Exbico\Underwriting\Tests\Traits\WithResponses;
use Exception;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Utils;
use JsonException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\ResponseInterface;

class CreditRatingNbchTest extends TestCase
{
    use WithClient;
    use WithResponses;

    /**
     * @throws JsonException
     * @throws ClientExceptionInterface
     * @throws Exception
     */
    public function testRequestReport(): void
    {
        $errorMessageBadRequest = "String too short";
        $errorMessageForbidden = "Insufficient funds";
        $requestId = random_int(1, 9999999);
        $client = $this->getClientWithMockHandler([
            $this->getRequestReportSuccessfulResponse($requestId),
            $this->getBadRequestResponse($errorMessageBadRequest),
            $this->getUnauthorizedResponse(),
            $this->getForbiddenResponse($errorMessageForbidden),
            $this->getTooManyRequestsResponse(),
        ]);
        $creditRatingNbch = new CreditRatingNbch($client);
        $person = $this->preparePerson();
        $document = $this->prepareDocument();
        // Success request
        $reportStatus = $creditRatingNbch->requestReport($person, $document);
        self::assertEquals($requestId, $reportStatus->getRequestId());
        self::assertEquals('inProgress', $reportStatus->getStatus());
        // Bad request
        $this->expectException(RequestValidationFailedException::class);
        $creditRatingNbch->requestReport($person, $document);
        // Unauthorized
        $this->expectException(UnauthorizedException::class);
        $creditRatingNbch->requestReport($person, $document);
        // Forbidden
        $this->expectException(ForbiddenException::class);
        $this->expectExceptionMessage($errorMessageForbidden);
        $creditRatingNbch->requestReport($person, $document);
        // Too many requests
        $this->expectException(TooManyRequestsException::class);
        $creditRatingNbch->requestReport($person, $document);
    }

    /**
     * @throws JsonException
     * @throws ClientExceptionInterface
     * @throws Exception
     */
    public function testLeadRequestReport(): void
    {
        $errorMessageBadRequest = "String too short";
        $errorMessageForbidden = "Insufficient funds";
        $requestId = random_int(1, 9999999);
        $client = $this->getClientWithMockHandler([
            $this->getRequestReportSuccessfulResponse($requestId),
            $this->getBadRequestResponse($errorMessageBadRequest),
            $this->getUnauthorizedResponse(),
            $this->getForbiddenResponse($errorMessageForbidden),
            $this->getTooManyRequestsResponse(),
        ]);
        $creditRatingNbch = new CreditRatingNbch($client);
        $leadId = random_int(1, 10000);
        $document = $this->prepareDocument();
        // Success request
        $reportStatus = $creditRatingNbch->requestLeadReport($leadId, $document);
        self::assertEquals($requestId, $reportStatus->getRequestId());
        self::assertEquals('inProgress', $reportStatus->getStatus());
        // Bad request
        $this->expectException(RequestValidationFailedException::class);
        $creditRatingNbch->requestLeadReport($leadId, $document);
        // Unauthorized
        $this->expectException(UnauthorizedException::class);
        $creditRatingNbch->requestLeadReport($leadId, $document);
        // Forbidden
        $this->expectException(ForbiddenException::class);
        $this->expectExceptionMessage($errorMessageForbidden);
        $creditRatingNbch->requestLeadReport($leadId, $document);
        // Too many requests
        $this->expectException(TooManyRequestsException::class);
        $creditRatingNbch->requestLeadReport($leadId, $document);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws Exception
     */
    public function testDownloadReport(): void
    {
        $bytes = random_bytes(16384);
        $client = $this->getClientWithMockHandler([
            $this->getDownloadReportSuccessfulResponse($bytes),
            $this->getReportNotReadyYetResponse()
        ]);
        $creditRatingNbch = new CreditRatingNbch($client);
        $tempFilename = tempnam(sys_get_temp_dir(), 'pdf');
        $creditRatingNbch->downloadPdfReport(1, $tempFilename);
        self::assertEquals($bytes, file_get_contents($tempFilename));
        unlink($tempFilename);
        // Report not ready
        $this->expectException(ReportNotReadyException::class);
        $creditRatingNbch->downloadPdfReport(1, 'test.pdf');
    }

    /**
     * @param int $requestId
     * @return Response
     * @throws JsonException
     */
    private function getRequestReportSuccessfulResponse(int $requestId): ResponseInterface
    {
        return new Response(200, [], json_encode([
            "requestId" => $requestId,
            "status" => "inProgress"
        ], JSON_THROW_ON_ERROR));
    }

    private function getDownloadReportSuccessfulResponse($resource): ResponseInterface
    {
        return new Response(200, [
            'Content-Type' => 'application/pdf',
        ], Utils::streamFor($resource));
    }

    private function getReportNotReadyYetResponse(): ResponseInterface
    {
        return new Response(422, [], "Report not ready");
    }

    private function preparePerson(): PersonDto
    {
        $person = new PersonDto();
        $person->setFirstname('Homer');
        $person->setMiddlename('Jay');
        $person->setLastname('Simpson');
        return $person;
    }

    private function prepareDocument(): DocumentDto
    {
        $document = new DocumentDto();
        $document->setNumber('230032');
        $document->setSeries('2323');
        return $document;
    }
}
