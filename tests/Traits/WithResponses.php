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
            "message" => $message
        ], JSON_THROW_ON_ERROR));
    }

    /**
     * @return ResponseInterface
     * @throws JsonException
     */
    public function getTooManyRequestsResponse(): ResponseInterface
    {
        return new Response(423, [], json_encode([
            "message" => "Too many requests"
        ], JSON_THROW_ON_ERROR));
    }
}