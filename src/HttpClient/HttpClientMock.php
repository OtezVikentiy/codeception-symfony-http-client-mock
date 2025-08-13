<?php

namespace OtezVikentiy\CodeceptionHttpMock\HttpClient;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Contracts\HttpClient\ResponseStreamInterface;

class HttpClientMock implements HttpClientInterface
{
    private static ?self $instance = null;
    private array $expectations = [];
    private array $requests = [];

    private function __construct() {}

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public static function reset(): void
    {
        self::$instance = null;
    }

    public function expect(string $method, string $url, bool $passInCaseUnused): RequestExpectation
    {
        $expectation = new RequestExpectation($method, $url, $passInCaseUnused);
        $this->expectations[] = $expectation;
        return $expectation;
    }

    public function request(string $method, string $url, array $options = []): ResponseInterface
    {
        $this->requests[] = ['method' => $method, 'url' => $url, 'options' => $options];

        foreach ($this->expectations as $key => $expectation) {
            if ($expectation->matches($method, $url, $options)) {
                unset($this->expectations[$key]);
                return $expectation->getResponse();
            }
        }

        throw new \RuntimeException("Unexpected request: $method $url");
    }

    public function verifyAllExpectations(): void
    {
        if (!empty($this->expectations)) {
            $expectations = array_values($this->expectations);
            $leftExpectations = [];

            /** @var RequestExpectation $expectation */
            foreach ($expectations as $expectation) {
                if (!$expectation->isPassInCaseUnused()) {
                    $leftExpectations[] = $expectation;
                }
            }

            if (empty($leftExpectations)) {
                return;
            }

            throw new \RuntimeException(sprintf(
                "Unmet HTTP expectations. Data [%s]",
                (string) $leftExpectations[0]
            ));
        }
    }

    public function stream(iterable|ResponseInterface $responses, ?float $timeout = null): ResponseStreamInterface
    {
        return new MockResponseStream();
    }

    public function withOptions(array $options): static
    {
        return $this;
    }
}