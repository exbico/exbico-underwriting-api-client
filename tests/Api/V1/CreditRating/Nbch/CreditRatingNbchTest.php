<?php

namespace Exbico\Underwriting\Tests\Api\V1\CreditRating\Nbch;

use Exbico\Underwriting\Api\V1\CreditRating\Nbch\CreditRatingNbch;
use Exbico\Underwriting\Dto\V1\Request\DocumentDto;
use Exbico\Underwriting\Dto\V1\Request\PersonDto;
use Exbico\Underwriting\Exception\ForbiddenException;
use Exbico\Underwriting\Exception\NotEnoughMoneyException;
use Exbico\Underwriting\Exception\ReportGettingErrorException;
use Exbico\Underwriting\Exception\ReportNotReadyException;
use Exbico\Underwriting\Exception\BadRequestException;
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
    public function testRequestReportSuccess(): void
    {
        $requestId = random_int(1, 9999999);
        $client = $this->getClientWithMockHandler([
            $this->getRequestReportSuccessfulResponse($requestId),
        ]);
        $creditRatingNbch = new CreditRatingNbch($client);
        $reportStatus = $creditRatingNbch->requestReport($this->preparePerson(), $this->prepareDocument());
        self::assertEquals($requestId, $reportStatus->getRequestId());
        self::assertEquals('inProgress', $reportStatus->getStatus());
    }

    /**
     * @throws JsonException
     * @throws ClientExceptionInterface
     */
    public function testRequestReportWhenNotEnoughMoney(): void
    {
        $client = $this->getClientWithMockHandler([
            $this->getNotEnoughMoneyResponse(),
        ]);
        $creditRatingNbch = new CreditRatingNbch($client);
        $this->expectException(NotEnoughMoneyException::class);
        $this->expectExceptionMessage('An error has occurred. Please check you have enough money to get this report.');
        $creditRatingNbch->requestReport($this->preparePerson(), $this->prepareDocument());
    }

    /**
     * @throws JsonException
     * @throws ClientExceptionInterface
     */
    public function testRequestReportWhenRequestIsBad(): void
    {
        $errorMessage = 'String is too short';
        $client = $this->getClientWithMockHandler([
            $this->getBadRequestResponse($errorMessage),
        ]);
        $creditRatingNbch = new CreditRatingNbch($client);
        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage($errorMessage);
        $creditRatingNbch->requestReport($this->preparePerson(), $this->prepareDocument());

    }

    /**
     * @throws JsonException
     * @throws ClientExceptionInterface
     */
    public function testRequestReportWhenUnauthorized(): void
    {
        $client = $this->getClientWithMockHandler([
            $this->getUnauthorizedResponse(),
        ]);
        $creditRatingNbch = new CreditRatingNbch($client);
        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessage('Wrong token');
        $creditRatingNbch->requestReport($this->preparePerson(), $this->prepareDocument());
    }

    /**
     * @throws JsonException
     * @throws ClientExceptionInterface
     */
    public function testRequestReportWhenAccessDenied(): void
    {
        $errorMessage = 'Access denied';
        $client = $this->getClientWithMockHandler([
            $this->getForbiddenResponse($errorMessage),
        ]);
        $creditRatingNbch = new CreditRatingNbch($client);
        $this->expectException(ForbiddenException::class);
        $this->expectExceptionMessage($errorMessage);
        $creditRatingNbch->requestReport($this->preparePerson(), $this->prepareDocument());
    }

    /**
     * @throws JsonException
     * @throws ClientExceptionInterface
     */
    public function testRequestReportWhenTooManyRequests(): void
    {
        $client = $this->getClientWithMockHandler([
            $this->getTooManyRequestsResponse(),
        ]);
        $creditRatingNbch = new CreditRatingNbch($client);
        $this->expectException(TooManyRequestsException::class);
        $this->expectExceptionMessage('Too many requests');
        $creditRatingNbch->requestReport($this->preparePerson(), $this->prepareDocument());
    }

    /**
     * @throws JsonException
     * @throws ClientExceptionInterface
     */
    public function testRequestReportWhenReportGettingError(): void
    {
        $client = $this->getClientWithMockHandler([
            $this->getReportGettingErrorResponse(),
        ]);
        $creditRatingNbch = new CreditRatingNbch($client);
        $this->expectException(ReportGettingErrorException::class);
        $creditRatingNbch->requestReport($this->preparePerson(), $this->prepareDocument());
    }

    /**
     * @throws JsonException
     * @throws ClientExceptionInterface
     * @throws Exception
     */
    public function testRequestLeadReportSuccess(): void
    {
        $requestId = random_int(1, 9999999);
        $leadId = random_int(1, 9999999);
        $client = $this->getClientWithMockHandler([
            $this->getRequestReportSuccessfulResponse($requestId),
        ]);
        $creditRatingNbch = new CreditRatingNbch($client);
        $reportStatus = $creditRatingNbch->requestLeadReport($leadId, $this->prepareDocument());
        self::assertEquals($requestId, $reportStatus->getRequestId());
        self::assertEquals('inProgress', $reportStatus->getStatus());
    }

    /**
     * @throws JsonException
     * @throws ClientExceptionInterface
     * @throws Exception
     */
    public function testRequestLeadReportWhenNotEnoughMoney(): void
    {
        $leadId = random_int(1, 9999999);
        $client = $this->getClientWithMockHandler([
            $this->getNotEnoughMoneyResponse(),
        ]);
        $creditRatingNbch = new CreditRatingNbch($client);
        $this->expectException(NotEnoughMoneyException::class);
        $this->expectExceptionMessage('An error has occurred. Please check you have enough money to get this report.');
        $creditRatingNbch->requestLeadReport($leadId, $this->prepareDocument());
    }

    /**
     * @throws Exception
     * @throws JsonException
     * @throws ClientExceptionInterface
     */
    public function testRequestLeadReportWhenRequestIsBad(): void
    {
        $leadId = random_int(1, 9999999);
        $errorMessage = 'String is too short';
        $client = $this->getClientWithMockHandler([
            $this->getBadRequestResponse($errorMessage),
        ]);
        $creditRatingNbch = new CreditRatingNbch($client);
        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage($errorMessage);
        $creditRatingNbch->requestLeadReport($leadId, $this->prepareDocument());
    }

    /**
     * @throws Exception
     * @throws JsonException
     * @throws ClientExceptionInterface
     */
    public function testRequestLeadReportWhenUnauthorized(): void
    {
        $leadId = random_int(1, 9999999);
        $client = $this->getClientWithMockHandler([
            $this->getUnauthorizedResponse(),
        ]);
        $creditRatingNbch = new CreditRatingNbch($client);
        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessage('Wrong token');
        $creditRatingNbch->requestLeadReport($leadId, $this->prepareDocument());
    }

    /**
     * @throws Exception
     * @throws JsonException
     * @throws ClientExceptionInterface
     */
    public function testRequestLeadReportWhenAccessDenied(): void
    {
        $leadId = random_int(1, 9999999);
        $errorMessage = 'Access denied';
        $client = $this->getClientWithMockHandler([
            $this->getForbiddenResponse($errorMessage),
        ]);
        $creditRatingNbch = new CreditRatingNbch($client);
        $this->expectException(ForbiddenException::class);
        $this->expectExceptionMessage($errorMessage);
        $creditRatingNbch->requestLeadReport($leadId, $this->prepareDocument());
    }

    /**
     * @throws JsonException
     * @throws ClientExceptionInterface
     * @throws Exception
     */
    public function testRequestLeadReportWhenTooManyRequests(): void
    {
        $leadId = random_int(1, 9999999);
        $client = $this->getClientWithMockHandler([
            $this->getTooManyRequestsResponse(),
        ]);
        $creditRatingNbch = new CreditRatingNbch($client);
        $this->expectException(TooManyRequestsException::class);
        $this->expectExceptionMessage('Too many requests');
        $creditRatingNbch->requestLeadReport($leadId, $this->prepareDocument());
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
