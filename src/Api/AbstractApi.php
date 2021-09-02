<?php
declare(strict_types=1);

namespace Exbico\Underwriting\Api;

use Exbico\Underwriting\Client;
use Exbico\Underwriting\Exception\ForbiddenException;
use Exbico\Underwriting\Exception\HttpException;
use Exbico\Underwriting\Exception\NotFoundException;
use Exbico\Underwriting\Exception\BadRequestException;
use Exbico\Underwriting\Exception\RequestPreparationException;
use Exbico\Underwriting\Exception\ResponseParsingException;
use Exbico\Underwriting\Exception\ServerErrorException;
use Exbico\Underwriting\Exception\TooManyRequestsException;
use Exbico\Underwriting\Exception\UnauthorizedException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Psr7\UriResolver;
use GuzzleHttp\Psr7\Utils;
use InvalidArgumentException;
use JsonException;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use RuntimeException;

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
     * @throws ForbiddenException
     * @throws HttpException
     * @throws NotFoundException
     * @throws ServerErrorException
     * @throws TooManyRequestsException
     * @throws UnauthorizedException
     * @throws RequestPreparationException
     * @throws ClientExceptionInterface
     * @throws ResponseParsingException
     */
    protected function sendRequest(RequestInterface $request): ResponseInterface
    {
        try {
            $baseUri = $this->getBaseUri();
            $request = $this->signRequest($request)
                ->withHeader('User-Agent', self::HEADER_USER_AGENT)
                ->withUri(UriResolver::resolve($baseUri, $request->getUri()));
            $this->getClient()->getLogger()->debug('Send request', [
                'url' => (string)$request->getUri(),
                'body' => $request->getBody()->getContents()
            ]);
        } catch (InvalidArgumentException | RuntimeException $exception) {
            throw new RequestPreparationException($exception->getMessage(), $exception->getCode(), $exception);
        }
        $response = $this->getClient()->getHttpClient()->sendRequest($request);
        $this->getClient()->getLogger()->debug('Receive response', [
            'status' => $response->getStatusCode(),
        ]);
        $this->checkForErrors($response);
        return $response;
    }

    /**
     * @throws ResponseParsingException
     */
    protected function download(ResponseInterface $response, string $savePath): void
    {
        try {
            $fh = fopen($savePath, 'wb+');
            while ($response->getBody()->eof() === false) {
                fwrite($fh, $response->getBody()->read(8192));
            }
            fclose($fh);
        } catch (RuntimeException $exception) {
            throw new ResponseParsingException('Unable to read stream', $exception->getCode(), $exception);
        }
    }

    protected function makeRequest(string $method, string $path): RequestInterface
    {
        return new Request($method, $path);
    }

    /**
     * @param array $body
     * @return StreamInterface
     * @throws RequestPreparationException
     */
    protected function prepareRequestBody(array $body): StreamInterface
    {
        try {
            return Utils::streamFor(json_encode($body, JSON_THROW_ON_ERROR));
        } catch (InvalidArgumentException | JsonException $exception) {
            throw new RequestPreparationException(
                'Request preparation error',
                $exception->getCode(),
                $exception
            );
        }
    }

    /**
     * @param ResponseInterface $response
     * @return array
     * @throws ResponseParsingException
     */
    protected function parseResponseResult(ResponseInterface $response): array
    {
        try {
            $response->getBody()->rewind();
            return json_decode(
                $response->getBody()->getContents(),
                true,
                512,
                JSON_THROW_ON_ERROR
            );
        } catch (RuntimeException | JsonException $exception) {
            throw new ResponseParsingException(
                'Unable to parse response',
                $exception->getCode(),
                $exception
            );
        }
    }

    /**
     * @throws InvalidArgumentException
     */
    private function getBaseUri(): Uri
    {
        return (new Uri($this->getClient()->getApiSettings()->getBaseUrl()))
            ->withPath('/' . implode('/', [
                    $this->getClient()->getApiSettings()->getApiBasePath(),
                    $this->getApiVersion(),
                ]) . '/');
    }

    /**
     * @throws InvalidArgumentException
     */
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
     * @throws ResponseParsingException
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
            throw new HttpException('Unknown API response', $response->getStatusCode());
        }
    }

    private function isResponseSuccess(ResponseInterface $response): bool
    {
        return $response->getStatusCode() >= 200
            && $response->getStatusCode() <= 204;
    }

    /**
     * @param ResponseInterface $response
     * @throws BadRequestException
     * @throws ResponseParsingException
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
     * @throws ResponseParsingException
     */
    private function checkForUnauthorized(ResponseInterface $response): void
    {
        if ($response->getStatusCode() === UnauthorizedException::HTTP_STATUS) {
            try {
                $contents = $response->getBody()->getContents();
            } catch (RuntimeException $exception) {
                throw new ResponseParsingException(
                    'Unable to parse response',
                    $exception->getCode(),
                    $exception
                );
            }
            throw new UnauthorizedException($contents);
        }
    }

    /**
     * @param ResponseInterface $response
     * @throws ForbiddenException
     * @throws ResponseParsingException
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
     * @throws NotFoundException
     * @throws ResponseParsingException
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
     * @throws ServerErrorException
     * @throws ResponseParsingException
     */
    private function checkForServerError(ResponseInterface $response): void
    {
        if ($response->getStatusCode() === ServerErrorException::HTTP_STATUS) {
            $contents = $this->parseResponseResult($response);
            throw new ServerErrorException($contents['message'] ?? 'Unknown server error');
        }
    }
}
