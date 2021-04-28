<?php
declare(strict_types=1);

namespace Exbico\Underwriting\Api;

use Exbico\Underwriting\Client;
use Exbico\Underwriting\Exception\ForbiddenException;
use Exbico\Underwriting\Exception\HttpException;
use Exbico\Underwriting\Exception\NotFoundException;
use Exbico\Underwriting\Exception\BadRequestException;
use Exbico\Underwriting\Exception\ServerErrorException;
use Exbico\Underwriting\Exception\TooManyRequestsException;
use Exbico\Underwriting\Exception\UnauthorizedException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Psr7\UriResolver;
use GuzzleHttp\Psr7\Utils;
use JsonException;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

abstract class AbstractApi
{
    private const HEADER_USER_AGENT = 'Exbico PHP API Client';
    private const HEADER_TOKEN_KEY = 'Authorization';

    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    abstract public function getApiVersion(): string;

    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * @param RequestInterface $request
     * @return ResponseInterface
     * @throws BadRequestException
     * @throws UnauthorizedException
     * @throws ForbiddenException
     * @throws NotFoundException
     * @throws TooManyRequestsException
     * @throws ServerErrorException
     * @throws HttpException
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    protected function sendRequest(RequestInterface $request): ResponseInterface
    {
        $baseUri = $this->getBaseUri();
        $request = $this->signRequest($request)
            ->withHeader('User-Agent', self::HEADER_USER_AGENT)
            ->withUri(UriResolver::resolve($baseUri, $request->getUri()));
        $this->getClient()->getLogger()->debug('Send request', [
            'url' => (string)$request->getUri(),
            'body' => $request->getBody()->getContents()
        ]);
        $response = $this->getClient()->getHttpClient()->sendRequest($request);
        $this->getClient()->getLogger()->debug('Receive response', [
            'status' => $response->getStatusCode(),
        ]);
        $this->checkForErrors($response);
        return $response;
    }

    protected function download(ResponseInterface $response, string $savePath): void
    {
        $fh = fopen($savePath, 'wb+');
        while ($response->getBody()->eof() === false) {
            fwrite($fh, $response->getBody()->read(8192));
        }
        fclose($fh);
    }

    protected function makeRequest(string $method, string $path): RequestInterface
    {
        return new Request($method, $path);
    }

    /**
     * @param array $body
     * @return StreamInterface
     * @throws JsonException
     */
    protected function prepareRequestBody(array $body): StreamInterface
    {
        return Utils::streamFor(json_encode($body, JSON_THROW_ON_ERROR));
    }

    /**
     * @param ResponseInterface $response
     * @return array
     * @throws JsonException
     */
    protected function parseResponseResult(ResponseInterface $response): array
    {
        $response->getBody()->rewind();
        return json_decode(
            $response->getBody()->getContents(),
            true, 512,
            JSON_THROW_ON_ERROR
        );
    }

    private function getBaseUri(): Uri
    {
        return (new Uri($this->getClient()->getApiSettings()->getBaseUrl()))
            ->withPath(implode('/', [
                    $this->getClient()->getApiSettings()->getApiBasePath(),
                    $this->getApiVersion(),
                ]) . '/');
    }

    private function signRequest(RequestInterface $request): RequestInterface
    {
        $token = $this->getClient()->getApiSettings()->getToken();
        return $request->withHeader(self::HEADER_TOKEN_KEY, $token);
    }

    /**
     * @param ResponseInterface $response
     * @throws BadRequestException
     * @throws UnauthorizedException
     * @throws ForbiddenException
     * @throws NotFoundException
     * @throws TooManyRequestsException
     * @throws ServerErrorException
     * @throws HttpException
     * @throws JsonException
     */
    protected function checkForErrors(ResponseInterface $response): void
    {
        $this->checkForBadRequest($response);
        $this->checkForUnauthorized($response);
        $this->checkForForbidden($response);
        $this->checkForNotFound($response);
        $this->checkForTooManyRequests($response);
        $this->checkForServerError($response);
        if (!$this->isResponseSuccess($response)) {
            throw new HttpException($response->getBody()->getContents(), $response->getStatusCode());
        }
    }

    private function isResponseSuccess(ResponseInterface $response): bool
    {
        return $response->getStatusCode() >= 200
            && $response->getStatusCode() <= 204;
    }

    /**
     * @param ResponseInterface $response
     * @throws JsonException
     * @throws BadRequestException
     */
    private function checkForBadRequest(ResponseInterface $response): void
    {
        if ($response->getStatusCode() === BadRequestException::HTTP_STATUS) {
            $result = $this->parseResponseResult($response);
            throw new BadRequestException($result['message'] ?? 'Unknown error');
        }
    }

    /**
     * @param ResponseInterface $response
     * @throws UnauthorizedException
     */
    private function checkForUnauthorized(ResponseInterface $response): void
    {
        if ($response->getStatusCode() === UnauthorizedException::HTTP_STATUS) {
            throw new UnauthorizedException($response->getBody()->getContents());
        }
    }

    /**
     * @param ResponseInterface $response
     * @throws JsonException
     * @throws BadRequestException
     */
    private function checkForForbidden(ResponseInterface $response): void
    {
        if ($response->getStatusCode() === ForbiddenException::HTTP_STATUS) {
            $contents = $this->parseResponseResult($response);
            throw new ForbiddenException($contents['message'] ?? 'Forbidden error');
        }
    }

    /**
     * @param ResponseInterface $response
     * @throws JsonException
     * @throws NotFoundException
     */
    private function checkForNotFound(ResponseInterface $response): void
    {
        if ($response->getStatusCode() === NotFoundException::HTTP_STATUS) {
            $contents = $this->parseResponseResult($response);
            throw new NotFoundException($contents['message'] ?? 'Not found');
        }
    }

    /**
     * @param ResponseInterface $response
     * @throws TooManyRequestsException
     */
    private function checkForTooManyRequests(ResponseInterface $response): void
    {
        if ($response->getStatusCode() === TooManyRequestsException::HTTP_STATUS) {
            throw new TooManyRequestsException('Too many requests');
        }
    }

    /**
     * @param ResponseInterface $response
     * @throws JsonException
     * @throws ServerErrorException
     */
    private function checkForServerError(ResponseInterface $response): void
    {
        if ($response->getStatusCode() === ServerErrorException::HTTP_STATUS) {
            $contents = $this->parseResponseResult($response);
            throw new ServerErrorException($contents['message'] ?? 'Unknown server error');
        }
    }
}