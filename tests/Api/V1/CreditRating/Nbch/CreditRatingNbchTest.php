<?php

namespace Exbico\Underwriting\Tests\Api\V1\CreditRating\Nbch;

use Exbico\Underwriting\Api\V1\CreditRating\Nbch\CreditRatingNbch;
use Exbico\Underwriting\Dto\V1\Request\DocumentDto;
use Exbico\Underwriting\Dto\V1\Request\PersonDto;
use Exbico\Underwriting\Exception\ForbiddenException;
use Exbico\Underwriting\Exception\HttpException;
use Exbico\Underwriting\Exception\LeadNotDistributedToContractException;
use Exbico\Underwriting\Exception\NotEnoughMoneyException;
use Exbico\Underwriting\Exception\NotFoundException;
use Exbico\Underwriting\Exception\ReportGettingErrorException;
use Exbico\Underwriting\Exception\ReportNotReadyException;
use Exbico\Underwriting\Exception\BadRequestException;
use Exbico\Underwriting\Exception\RequestPreparationException;
use Exbico\Underwriting\Exception\ResponseParsingException;
use Exbico\Underwriting\Exception\ServerErrorException;
use Exbico\Underwriting\Exception\TooManyRequestsException;
use Exbico\Underwriting\Exception\UnauthorizedException;
use Exbico\Underwriting\Tests\Traits\WithClient;
use Exbico\Underwriting\Tests\Traits\WithResponses;
use Exception;
use InvalidArgumentException;
use JsonException;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientExceptionInterface;
use RuntimeException;

class CreditRatingNbchTest extends TestCase
{
    use WithClient;
    use WithResponses;

    private const BAD_REQUEST_MESSAGE       = 'String is too short';
    private const FORBIDDEN_REQUEST_MESSAGE = 'Access denied';

    /**
     * @throws BadRequestException
     * @throws ForbiddenException
     * @throws HttpException
     * @throws NotFoundException
     * @throws ServerErrorException
     * @throws TooManyRequestsException
     * @throws UnauthorizedException
     * @throws InvalidArgumentException
     * @throws JsonException
     * @throws ExpectationFailedException
     * @throws ClientExceptionInterface
     * @throws RuntimeException
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function testRequestReport(): void
    {
        $requestId = random_int(1, 9999999);
        $creditRatingNbch = new CreditRatingNbch($this->getClientWithMockHandler([
            $this->getRequestReportSuccessfulResponse($requestId),
        ]));
        $reportStatus = $creditRatingNbch->requestReport($this->preparePerson(), $this->prepareDocument());
        self::assertEquals($requestId, $reportStatus->getRequestId());
        self::assertEquals('inProgress', $reportStatus->getStatus());
    }

    /**
     * @throws BadRequestException
     * @throws ForbiddenException
     * @throws HttpException
     * @throws NotFoundException
     * @throws ServerErrorException
     * @throws TooManyRequestsException
     * @throws UnauthorizedException
     * @throws InvalidArgumentException
     * @throws JsonException
     * @throws ClientExceptionInterface
     * @throws RuntimeException
     */
    public function testRequestReportWhenNotEnoughMoney(): void
    {
        $creditRatingNbch = new CreditRatingNbch($this->getClientWithMockHandler([
            $this->getNotEnoughMoneyResponse(),
        ]));
        $this->expectException(NotEnoughMoneyException::class);
        $this->expectExceptionMessage('An error has occurred. Please check you have enough money to get this report.');
        $creditRatingNbch->requestReport($this->preparePerson(), $this->prepareDocument());
    }

    /**
     * @throws BadRequestException
     * @throws ForbiddenException
     * @throws HttpException
     * @throws NotFoundException
     * @throws ServerErrorException
     * @throws TooManyRequestsException
     * @throws UnauthorizedException
     * @throws InvalidArgumentException
     * @throws JsonException
     * @throws ClientExceptionInterface
     * @throws RuntimeException
     */
    public function testRequestReportWhenBadRequest(): void
    {
        $creditRatingNbch = new CreditRatingNbch($this->getClientWithMockHandler([
            $this->getBadRequestResponse(self::BAD_REQUEST_MESSAGE),
        ]));
        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage(self::BAD_REQUEST_MESSAGE);
        $creditRatingNbch->requestReport($this->preparePerson(), $this->prepareDocument());
    }

    /**
     * @throws BadRequestException
     * @throws TooManyRequestsException
     * @throws RuntimeException
     * @throws ForbiddenException
     * @throws ClientExceptionInterface
     * @throws HttpException
     * @throws InvalidArgumentException
     * @throws ServerErrorException
     * @throws NotFoundException
     */
    public function testRequestReportWhenUnauthorized(): void
    {
        $creditRatingNbch = new CreditRatingNbch($this->getClientWithMockHandler([
            $this->getUnauthorizedResponse(),
        ]));
        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessage('Wrong token');
        $creditRatingNbch->requestReport($this->preparePerson(), $this->prepareDocument());
    }

    /**
     * @throws UnauthorizedException
     * @throws BadRequestException
     * @throws TooManyRequestsException
     * @throws JsonException
     * @throws RuntimeException
     * @throws ClientExceptionInterface
     * @throws InvalidArgumentException
     * @throws HttpException
     * @throws ServerErrorException
     * @throws NotFoundException
     */
    public function testRequestReportWhenForbidden(): void
    {
        $creditRatingNbch = new CreditRatingNbch($this->getClientWithMockHandler([
            $this->getForbiddenResponse(self::FORBIDDEN_REQUEST_MESSAGE),
        ]));
        $this->expectException(ForbiddenException::class);
        $this->expectExceptionMessage(self::FORBIDDEN_REQUEST_MESSAGE);
        $creditRatingNbch->requestReport($this->preparePerson(), $this->prepareDocument());
    }

    /**
     * @throws BadRequestException
     * @throws UnauthorizedException
     * @throws RuntimeException
     * @throws ForbiddenException
     * @throws ClientExceptionInterface
     * @throws HttpException
     * @throws InvalidArgumentException
     * @throws ServerErrorException
     * @throws NotFoundException
     */
    public function testRequestReportWhenTooManyRequests(): void
    {
        $creditRatingNbch = new CreditRatingNbch($this->getClientWithMockHandler([
            $this->getTooManyRequestsResponse(),
        ]));
        $this->expectException(TooManyRequestsException::class);
        $this->expectExceptionMessage('Too many requests');
        $creditRatingNbch->requestReport($this->preparePerson(), $this->prepareDocument());
    }

    /**
     * @throws BadRequestException
     * @throws RequestPreparationException
     * @throws ForbiddenException
     * @throws ClientExceptionInterface
     * @throws InvalidArgumentException
     * @throws UnauthorizedException
     * @throws ExpectationFailedException
     * @throws ResponseParsingException
     * @throws TooManyRequestsException
     * @throws JsonException
     * @throws NotEnoughMoneyException
     * @throws InvalidArgumentException
     * @throws HttpException
     * @throws ServerErrorException
     * @throws Exception
     */
    public function testRequestLeadReport(): void
    {
        $requestId = random_int(1, 9999999);
        $leadId = random_int(1, 9999999);
        $creditRatingNbch = new CreditRatingNbch($this->getClientWithMockHandler([
            $this->getRequestReportSuccessfulResponse($requestId),
        ]));
        $reportStatus = $creditRatingNbch->requestLeadReport($leadId, $this->prepareDocument());
        self::assertEquals($requestId, $reportStatus->getRequestId());
        self::assertEquals('inProgress', $reportStatus->getStatus());
    }

    /**
     * @throws BadRequestException
     * @throws UnauthorizedException
     * @throws RequestPreparationException
     * @throws ResponseParsingException
     * @throws TooManyRequestsException
     * @throws JsonException
     * @throws ForbiddenException
     * @throws ClientExceptionInterface
     * @throws NotEnoughMoneyException
     * @throws InvalidArgumentException
     * @throws HttpException
     * @throws ServerErrorException
     * @throws Exception
     */
    public function testRequestLeadReportWhenLeadNotDistributed(): void
    {
        $leadId = random_int(1, 9999999);
        $creditRatingNbch = new CreditRatingNbch($this->getClientWithMockHandler([
            $this->getLeadNotDistributedToContractResponse(),
        ]));
        $this->expectException(LeadNotDistributedToContractException::class);
        $creditRatingNbch->requestLeadReport($leadId, $this->prepareDocument());
    }

    /**
     * @throws BadRequestException
     * @throws ForbiddenException
     * @throws HttpException
     * @throws NotEnoughMoneyException
     * @throws RequestPreparationException
     * @throws ResponseParsingException
     * @throws ServerErrorException
     * @throws TooManyRequestsException
     * @throws UnauthorizedException
     * @throws InvalidArgumentException
     * @throws JsonException
     * @throws ClientExceptionInterface
     * @throws Exception
     */
    public function testRequestLeadReportWhenNotEnoughMoney(): void
    {
        $leadId = random_int(1, 9999999);
        $creditRatingNbch = new CreditRatingNbch($this->getClientWithMockHandler([
            $this->getNotEnoughMoneyResponse(),
        ]));
        $this->expectException(NotEnoughMoneyException::class);
        $this->expectExceptionMessage('An error has occurred. Please check you have enough money to get this report.');
        $creditRatingNbch->requestLeadReport($leadId, $this->prepareDocument());
    }

    /**
     * @throws BadRequestException
     * @throws RequestPreparationException
     * @throws ForbiddenException
     * @throws ClientExceptionInterface
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws UnauthorizedException
     * @throws ExpectationFailedException
     * @throws ResponseParsingException
     * @throws TooManyRequestsException
     * @throws HttpException
     * @throws InvalidArgumentException
     * @throws ServerErrorException
     * @throws Exception
     */
    public function testDownloadReport(): void
    {
        $bytes = random_bytes(16384);
        $creditRatingNbch = new CreditRatingNbch($this->getClientWithMockHandler([
            $this->getDownloadReportSuccessfulResponse($bytes),
        ]));
        $tempFilename = tempnam(sys_get_temp_dir(), 'pdf');
        $creditRatingNbch->downloadPdfReport(1, $tempFilename);
        self::assertEquals($bytes, file_get_contents($tempFilename));
        unlink($tempFilename);
    }

    /**
     * @throws BadRequestException
     * @throws ForbiddenException
     * @throws HttpException
     * @throws NotFoundException
     * @throws RequestPreparationException
     * @throws ResponseParsingException
     * @throws ServerErrorException
     * @throws TooManyRequestsException
     * @throws UnauthorizedException
     * @throws ClientExceptionInterface
     */
    public function testDownloadReportWhenReportNotReadyYet(): void
    {
        $creditRatingNbch = new CreditRatingNbch($this->getClientWithMockHandler([
            $this->getReportNotReadyYetResponse(),
        ]));
        $this->expectException(ReportNotReadyException::class);
        $creditRatingNbch->downloadPdfReport(1, 'test.pdf');
    }

    /**
     * @throws BadRequestException
     * @throws UnauthorizedException
     * @throws RequestPreparationException
     * @throws ResponseParsingException
     * @throws TooManyRequestsException
     * @throws JsonException
     * @throws ForbiddenException
     * @throws ClientExceptionInterface
     * @throws HttpException
     * @throws ServerErrorException
     * @throws NotFoundException
     */
    public function testDownloadReportWhenReportGettingError(): void
    {
        $creditRatingNbch = new CreditRatingNbch($this->getClientWithMockHandler([
            $this->getReportGettingErrorResponse(),
        ]));
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
