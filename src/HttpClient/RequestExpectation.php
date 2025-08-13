<?php

namespace OtezVikentiy\CodeceptionHttpMock\HttpClient;

use Symfony\Contracts\HttpClient\ResponseInterface;

class RequestExpectation
{
    private ?ResponseInterface $response = null;
    private ?string $expectedBody = null;
    private array $expectedHeaders = [];
    private ?array $expectedParameters = [];

    public function __construct(
        private readonly string $method,
        private readonly string $url,
        private readonly bool $passInCaseUnused
    ) {}

    public function withBody(string $body): self
    {
        $this->expectedBody = $body;
        return $this;
    }

    public function withParameters(array $parameters): self
    {
        $this->expectedParameters = $parameters;
        return $this;
    }

    public function withHeaders(array $headers): self
    {
        $this->expectedHeaders = $headers;
        return $this;
    }

    public function respondWith(int $status, array $headers = [], string $body = ''): self
    {
        $this->response = new MockResponse($status, $headers, $body);
        return $this;
    }

    public function matches(string $method, string $url, array $options): bool
    {
        if ($this->method !== $method || !str_ends_with($url, $this->url)) {
            return false;
        }

        if (
            !empty($this->expectedBody)
            && ($options['body'] ?? json_encode($options['json'], JSON_UNESCAPED_UNICODE) ?? null) !== $this->expectedBody
        ) {
            return false;
        }

        if (
            !empty($this->expectedParameters)
            && ($options['query'] ?? null) !== $this->expectedParameters
        ) {
            return false;
        }

        foreach ($this->expectedHeaders as $name => $value) {
            if (!isset($options['headers'][$name]) || $options['headers'][$name] !== $value) {
                return false;
            }
        }

        return true;
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response ?? new MockResponse(200);
    }

    public function __toString(): string
    {
        return sprintf("%s %s", $this->method, $this->url);
    }

    public function isPassInCaseUnused(): bool
    {
        return $this->passInCaseUnused;
    }
}