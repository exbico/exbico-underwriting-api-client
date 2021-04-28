<?php
declare(strict_types=1);

namespace Exbico\Underwriting\Tests\Traits;

use GuzzleHttp\Psr7\Response;
use JsonException;
use Psr\Http\Message\ResponseInterface;

trait WithResponses
{
    /**
     * @param string $message
     * @return ResponseInterface
     * @throws JsonException
     */
    public function getBadRequestResponse(string $message): ResponseInterface
    {
        return new Response(400, [], json_encode([
            "status" => "failed",
            "message" => $message
        ], JSON_THROW_ON_ERROR));
    }

    /**
     * @return ResponseInterface
     * @throws JsonException
     */
    public function getNotEnoughMoneyResponse(): ResponseInterface
    {
        return new Response(400, [], json_encode([
            "status" => "failed",
            "message" => "An error has occurred. Please check you have enough money to get this report.",
        ], JSON_THROW_ON_ERROR));
    }

    public function getUnauthorizedResponse(): ResponseInterface
    {
        return new Response(401, [], "Wrong token");
    }

    /**
     * @param string $message
     * @return ResponseInterface
     * @throws JsonException
     */
    public function getForbiddenResponse(string $message): ResponseInterface
    {
        return new Response(403, [], json_encode([
            "status" => "failed",
            "message" => $message
        ], JSON_THROW_ON_ERROR));
    }

    /**
     * @return ResponseInterface
     */
    public function getTooManyRequestsResponse(): ResponseInterface
    {
        return new Response(429, [], "Too many requests");
    }

    /**
     * @return ResponseInterface
     * @throws JsonException
     */
    public function getLeadNotDistributedToContractResponse(): ResponseInterface
    {
        return new Response(400, [], json_encode([
            "status" => "failed",
            "message" => "Lead with id 132932 was not distributed to your contract."
        ], JSON_THROW_ON_ERROR));
    }
}