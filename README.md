# Codeception Symfony HttpClient Mock
Mock module for Symfony + Codeception + HttpClient

## Installation

```bash
composer require otezvikentiy/codeception-symfony-http-client-mock --dev
```

## Configuration

1) Add to your Functional.suite.yaml

```yaml
modules:
    enabled:
        - OtezVikentiy\CodeceptionHttpMock\HttpMockModule
    config:
        OtezVikentiy\CodeceptionHttpMock\HttpMockModule:
            http_client_service_name: 'http_client'
```

2) Configure your Symfony services.yaml as follows

```yaml
when@test:
    services:
        _defaults:
            public: true

        # turn off project factory
        # replace this path with the path to your factory
        App\Infrastructure\HttpClient\HttpClientFactory:
            class: stdClass

        # replace the client with mock
        http_client:
            class: OtezVikentiy\CodeceptionHttpMock\HttpClient\HttpClientMock
            factory: ['OtezVikentiy\CodeceptionHttpMock\HttpClient\HttpClientMock', 'getInstance']
```

3) Usage in tests

```php
    final public function someTest(FunctionalTester $I): void
    {
        $I->expectHttpRequest('DELETE', '/example/v1/orders')
            ->withBody(json_encode([
                'id' => '01f5cfeb-007e-11e5-82bf-9c8e99f9e058',
            ]))
            ->respondWith(status: 200, body: json_encode([
                    'Result' => 'Ok',
                ], JSON_UNESCAPED_UNICODE));

        $I->sendPost('/api/v3', '{"field": "value"}';

        $I->seeResponseCodeIs(200);
    }
```