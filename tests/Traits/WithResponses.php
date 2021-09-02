<?php
declare(strict_types=1);

namespace Exbico\Underwriting\Tests\Traits;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Utils;
use InvalidArgumentException;
use JsonException;
use Psr\Http\Message\ResponseInterface;

trait WithResponses
{
    /**
     * @param int $requestId
     * @return Response
     * @throws JsonException
     */
    private function getRequestReportSuccessfulResponse(int $requestId): ResponseInterface
    {
        return new Response(200, [], json_encode([
            'requestId' => $requestId,
            'status' => 'inProgress'
        ], JSON_THROW_ON_ERROR));
    }

    /**
     * @param string $message
     * @return ResponseInterface
     * @throws JsonException
     */
    private function getBadRequestResponse(string $message): ResponseInterface
    {
        return new Response(400, [], json_encode([
            'status' => 'failed',
            'message' => $message
        ], JSON_THROW_ON_ERROR));
    }

    /**
     * @return ResponseInterface
     * @throws JsonException
     */
    private function getNotEnoughMoneyResponse(): ResponseInterface
    {
        return new Response(400, [], json_encode([
            'status' => 'failed',
            'message' => 'An error has occurred. Please check you have enough money to get this report.',
        ], JSON_THROW_ON_ERROR));
    }

    /**
     * @return ResponseInterface
     * @throws JsonException
     */
    private function getProductNotAvailableResponse(): ResponseInterface
    {
        return new Response(403, [], json_encode([
            'status' => 'failed',
            'message' => 'Requested product is not available for your account',
        ], JSON_THROW_ON_ERROR));
    }

    /**
     * @throws JsonException
     */
    private function getReportGettingErrorResponse(): ResponseInterface
    {
        return new Response(500, [], json_encode([
            'status' => 'failed',
            'message' => 'Report getting error',
        ], JSON_THROW_ON_ERROR));
    }

    private function getUnauthorizedResponse(): ResponseInterface
    {
        return new Response(401, [], 'Wrong token');
    }

    /**
     * @param string $message
     * @return ResponseInterface
     * @throws JsonException
     */
    private function getForbiddenResponse(string $message): ResponseInterface
    {
        return new Response(403, [], json_encode([
            'status' => 'failed',
            'message' => $message
        ], JSON_THROW_ON_ERROR));
    }

    /**
     * @return ResponseInterface
     */
    private function getTooManyRequestsResponse(): ResponseInterface
    {
        return new Response(429, [], 'Too many requests');
    }

    /**
     * @return ResponseInterface
     * @throws JsonException
     */
    private function getLeadNotDistributedToContractResponse(): ResponseInterface
    {
        return new Response(403, [], json_encode([
            'status' => 'failed',
            'message' => 'Lead with id 132932 was not distributed to your contract.'
        ], JSON_THROW_ON_ERROR));
    }

    /**
     * @throws InvalidArgumentException
     */
    private function getDownloadReportSuccessfulResponse($resource): ResponseInterface
    {
        return new Response(200, [
            'Content-Type' => 'application/pdf',
        ], Utils::streamFor($resource));
    }

    private function getReportNotReadyYetResponse(): ResponseInterface
    {
        return new Response(422, [], 'Report not ready');
    }
}
