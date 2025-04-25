<?php

namespace OtezVikentiy\CodeceptionHttpMock;

use Codeception\Module;
use Codeception\TestInterface;
use OtezVikentiy\CodeceptionHttpMock\HttpClient\HttpClientMock;
use OtezVikentiy\CodeceptionHttpMock\HttpClient\RequestExpectation;

class HttpMockModule extends Module
{
    public function _beforeSuite($settings = []): void
    {
        $clientName = $settings['modules']['config'][self::class]['http_client_service_name'];
        $mock = HttpClientMock::getInstance();
        $this->getModule('Symfony')->_getContainer()->set($clientName, $mock);
    }

    public function _before(TestInterface $test): void
    {
        HttpClientMock::reset();
    }

    public function _after(TestInterface $test): void
    {
        HttpClientMock::getInstance()->verifyAllExpectations();
    }

    public function expectHttpRequest(string $method, string $url): RequestExpectation
    {
        return HttpClientMock::getInstance()->expect($method, $url);
    }
}