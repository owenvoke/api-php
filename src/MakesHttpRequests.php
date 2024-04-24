<?php

declare(strict_types=1);

namespace RetroAchievements\Api;

use Psr\Http\Message\ResponseInterface;
use RetroAchievements\Api\Exceptions\GenericException;
use RetroAchievements\Api\Exceptions\NotFoundException;
use RetroAchievements\Api\Exceptions\UnauthorizedException;

trait MakesHttpRequests
{
    protected function get(string $uri)
    {
        return $this->request('GET', $uri);
    }

    protected function post(string $uri, array $payload = [])
    {
        return $this->request('POST', $uri, $payload);
    }

    protected function put(string $uri, array $payload = [])
    {
        return $this->request('PUT', $uri, $payload);
    }

    protected function delete(string $uri, array $payload = [])
    {
        return $this->request('DELETE', $uri, $payload);
    }

    protected function request(string $verb, string $uri, array $payload = [])
    {
        $response = $this->client->request(
            $verb,
            $uri,
            empty($payload) ? [] : ['form_params' => $payload]
        );

        if (! $this->isSuccessful($response)) {
            $this->handleRequestError($response);
        }

        $responseBody = (string) $response->getBody();

        return json_decode($responseBody, true) ?: $responseBody;
    }

    protected function isSuccessful(ResponseInterface $response): bool
    {
        return (int) substr((string) $response->getStatusCode(), 0, 1) === 2;
    }

    protected function handleRequestError(ResponseInterface $response): void
    {
        if ($response->getStatusCode() === 404) {
            throw new NotFoundException((string) $response->getBody());
        }

        if ($response->getStatusCode() === 401) {
            throw new UnauthorizedException((string) $response->getBody());
        }

        throw new GenericException((string) $response->getBody());
    }
}
