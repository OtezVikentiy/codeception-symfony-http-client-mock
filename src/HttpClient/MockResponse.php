<?php

namespace OtezVikentiy\CodeceptionHttpMock\HttpClient;

use Symfony\Contracts\HttpClient\ResponseInterface;

class MockResponse implements ResponseInterface
{
    private array $info;

    public function __construct(
        private readonly int $statusCode,
        private readonly array $headers = [],
        private readonly string $content = ''
    ) {
        $this->info = [
            'http_code' => $this->statusCode,
            'response_headers' => $this->headers,
        ];
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getHeaders(bool $throw = true): array
    {
        return $this->headers;
    }

    public function getContent(bool $throw = true): string
    {
        return $this->content;
    }

    public function toArray(bool $throw = true): array
    {
        return json_decode($this->content, true) ?? [];
    }

    public function cancel(): void
    {
        // No-op
    }

    public function getInfo(?string $type = null): mixed
    {
        if ($type === null) {
            return $this->info;
        }
        return $this->info[$type] ?? null;
    }
}