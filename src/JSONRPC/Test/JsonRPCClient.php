<?php

declare(strict_types=1);

namespace App\JSONRPC\Test;

use function assert;
use Datto\JsonRpc\Client;
use Datto\JsonRpc\Responses\ErrorResponse;
use Datto\JsonRpc\Responses\Response;
use Datto\JsonRpc\Responses\ResultResponse;
use function is_array;
use function is_string;
use function ksort;
use LogicException;
use Sentry\Util\JSON;
use function sprintf;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

final class JsonRPCClient extends KernelBrowser
{
    private string $endpoint = '/api/jsonrpc';

    public function successJsonrpc(string $method, array $params = []): ResultResponse
    {
        $response = $this->jsonrpc($method, $params);

        if (!$response instanceof ResultResponse) {
            assert($response instanceof ErrorResponse);

            throw new LogicException(sprintf('Method "%s", code "%s", message "%s", data: "%s', $method, $response->getCode(), $response->getMessage(), JSON::encode($response->getData())));
        }

        $value = $response->getValue();
        if (is_array($value)) {
            self::deepSort($value);

            return new ResultResponse($response->getId(), $value);
        }

        return $response;
    }

    public function errorJsonrpc(string $method, array $params = []): ErrorResponse
    {
        $response = $this->jsonrpc($method, $params);

        assert($response instanceof ErrorResponse);

        return $response;
    }

    public function jsonrpc(string $method, array $params = []): Response
    {
        $client = new Client();

        $request = $client->query(1, $method, $params)->encode();

        $this->request('POST', $this->endpoint, [], [], $this->getHeaders(), $request);

        $response = $this->getResponse()->getContent();
        assert(is_string($response));

        $responses = $client->decode($response);
        if ([] === $responses) {
            throw new LogicException(sprintf('Method "%s" return wrong json "%s", status code: %s', $method, $response, $this->getResponse()->getStatusCode()));
        }

        return $responses[0];
    }

    private function getHeaders(): array
    {
        return [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_ACCEPT_LANGUAGE' => 'RU',
        ];
    }

    private static function deepSort(array &$array): void
    {
        ksort($array);

        foreach ($array as &$value) {
            if (is_array($value)) {
                self::deepSort($value);
            }
        }
    }
}
