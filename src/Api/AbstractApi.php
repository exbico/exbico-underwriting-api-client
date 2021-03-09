<?php
declare(strict_types=1);

namespace Exbico\Api;

use Exbico\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Psr7\UriResolver;
use GuzzleHttp\Psr7\Utils;
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
     * @throws ClientExceptionInterface
     */
    protected function sendRequest(RequestInterface $request): ResponseInterface
    {
        $baseUri = $this->getBaseUri();
        $request = $this->signRequest($request)
            ->withHeader('User-Agent', self::HEADER_USER_AGENT)
            ->withUri(UriResolver::resolve($baseUri, $request->getUri()));
        return $this->getClient()->getHttpClient()->sendRequest($request);
    }

    protected function download(ResponseInterface $response, string $savePath): void
    {
        $fh = fopen($savePath, 'wb+');
        while($response->getBody()->eof() === false) {
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
     * @throws \JsonException
     */
    protected function prepareRequestBody(array $body): StreamInterface
    {
        return Utils::streamFor(json_encode($body, JSON_THROW_ON_ERROR));
    }

    /**
     * @param ResponseInterface $response
     * @return array
     * @throws \JsonException
     */
    protected function parseResponseResult(ResponseInterface $response): array
    {
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

    private function signRequest($request): RequestInterface
    {
        $token = $this->getClient()->getApiSettings()->getToken();
        return $request->withHeader(self::HEADER_TOKEN_KEY, $token);
    }
}