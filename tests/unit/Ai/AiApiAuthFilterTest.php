<?php

namespace Tests\Unit\Ai;

use App\Filters\AiApiAuthFilter;
use App\Services\Ai\ApiKeyAuthenticator;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Test\CIUnitTestCase;

final class AiApiAuthFilterTest extends CIUnitTestCase
{
    protected function tearDown(): void
    {
        session()->destroy();
        parent::tearDown();
    }

    public function testRejectsRequestWithoutApiKeyHeader(): void
    {
        $response = (new AiApiAuthFilter())->before(service('request'));

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame(401, $response->getStatusCode());
        $this->assertStringContainsString('authentication_required', $response->getBody());
    }

    public function testRejectsInvalidApiKey(): void
    {
        $authenticator = $this->createMock(ApiKeyAuthenticator::class);
        $authenticator->expects($this->once())->method('findActiveUser')->with('incorreta')->willReturn(null);
        service('request')->setHeader('APIKEY', 'incorreta');

        $response = (new AiApiAuthFilter($authenticator))->before(service('request'));

        $this->assertSame(401, $response->getStatusCode());
        $this->assertStringContainsString('invalid_apikey', $response->getBody());
    }

    public function testAcceptsActiveApiKeyAndCreatesAuthenticatedSession(): void
    {
        $authenticator = $this->createMock(ApiKeyAuthenticator::class);
        $authenticator->expects($this->once())->method('findActiveUser')->with('correta')->willReturn([
            'id_us' => 10,
            'us_nome' => 'Teste',
            'us_email' => 'teste@example.com',
            'us_apikey' => 'correta',
        ]);
        service('request')->setHeader('APIKEY', 'correta');

        $this->assertNull((new AiApiAuthFilter($authenticator))->before(service('request')));
        $this->assertSame(10, session()->get('user_id'));
        $this->assertSame('correta', session()->get('apikey'));
    }
}
