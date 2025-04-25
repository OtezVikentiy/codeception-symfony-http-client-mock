<?php

namespace OtezVikentiy\CodeceptionHttpMock\HttpClient;

use LogicException;
use Symfony\Contracts\HttpClient\ChunkInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Contracts\HttpClient\ResponseStreamInterface;

class MockResponseStream implements ResponseStreamInterface
{
    public function __destruct() {}

    public function key(): ResponseInterface
    {
        throw new LogicException('Not implemented');
    }

    public function current(): ChunkInterface
    {
        throw new LogicException('Not implemented');
    }

    public function next(): void
    {
        // No-op
    }

    public function rewind(): void
    {
        // No-op
    }

    public function valid(): bool
    {
        return false;
    }
}