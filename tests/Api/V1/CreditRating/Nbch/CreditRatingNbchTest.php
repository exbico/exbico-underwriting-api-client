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
use JsonException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientExceptionInterface;

class CreditRatingNbchTest extends TestCase
{
    use WithClient;
    use WithResponses;

    private const BAD_REQUEST_MESSAGE       = 'String is too short';
    private const FORBIDDEN_REQUEST_MESSAGE = 'Access denied';

    /**
     * @throws JsonException
     * @throws ClientExceptionInterface
     * @throws Exception
     */
    public function testRequestReport(): void
    {
        $requestId = random_int(1, 9999999);
        $client = $this->getClientWithMockHandler([
            $this->getRequestReportSuccessfulResponse($requestId),
            $this->getNotEnoughMoneyResponse(),
            $this->getBadRequestResponse(self::BAD_REQUEST_MESSAGE),
            $this->getUnauthorizedResponse(),
            $this->getForbiddenResponse(self::FORBIDDEN_REQUEST_MESSAGE),
            $this->getTooManyRequestsResponse()
        ]);
        $creditRatingNbch = new CreditRatingNbch($client);
        // Successful case
        $reportStatus = $creditRatingNbch->requestReport($this->preparePerson(), $this->prepareDocument());
        self::assertEquals($requestId, $reportStatus->getRequestId());
        self::assertEquals('inProgress', $reportStatus->getStatus());
        // Not enough money
        $this->expectException(NotEnoughMoneyException::class);
        $this->expectExceptionMessage('An error has occurred. Please check you have enough money to get this report.');
        $creditRatingNbch->requestReport($this->preparePerson(), $this->prepareDocument());
        // Bad request
        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage(self::BAD_REQUEST_MESSAGE);
        $personWithoutName = new PersonDto();
        $creditRatingNbch->requestReport($personWithoutName, $this->prepareDocument());
        // Unauthorized
        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessage('Wrong token');
        $creditRatingNbch->requestReport($this->preparePerson(), $this->prepareDocument());
        // Forbidden
        $this->expectException(ForbiddenException::class);
        $this->expectExceptionMessage(self::FORBIDDEN_REQUEST_MESSAGE);
        $creditRatingNbch->requestReport($this->preparePerson(), $this->prepareDocument());
        // Too many requests
        $this->expectException(TooManyRequestsException::class);
        $this->expectExceptionMessage('Too many requests');
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
            $this->getNotEnoughMoneyResponse(),
        ]);
        $creditRatingNbch = new CreditRatingNbch($client);
        // Successful case
        $reportStatus = $creditRatingNbch->requestLeadReport($leadId, $this->prepareDocument());
        self::assertEquals($requestId, $reportStatus->getRequestId());
        self::assertEquals('inProgress', $reportStatus->getStatus());
        // Not enough money
        $this->expectException(NotEnoughMoneyException::class);
        $this->expectExceptionMessage('An error has occurred. Please check you have enough money to get this report.');
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
            $this->getReportNotReadyYetResponse(),
            $this->getReportGettingErrorResponse(),
        ]);
        $creditRatingNbch = new CreditRatingNbch($client);
        $tempFilename = tempnam(sys_get_temp_dir(), 'pdf');
        $creditRatingNbch->downloadPdfReport(1, $tempFilename);
        self::assertEquals($bytes, file_get_contents($tempFilename));
        unlink($tempFilename);
        // Report not ready
        $this->expectException(ReportNotReadyException::class);
        $creditRatingNbch->downloadPdfReport(1, 'test.pdf');
        // Report getting error
        $this->expectException(ReportGettingErrorException::class);
        $creditRatingNbch->downloadPdfReport(-1, 'test.pdf');
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
