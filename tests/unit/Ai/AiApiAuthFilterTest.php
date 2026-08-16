<?php

namespace Tests\Unit\Ai;

use App\Filters\AiApiAuthFilter;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Test\CIUnitTestCase;

final class AiApiAuthFilterTest extends CIUnitTestCase
{
    protected function tearDown(): void
    {
        session()->destroy();
        parent::tearDown();
    }

    public function testRejectsRequestWhenSessionHasNoApiKey(): void
    {
        session()->set(['user' => 'Teste', 'user_id' => 10]);
        service('request')->setHeader('APIKEY', 'abc');

        $response = (new AiApiAuthFilter())->before(service('request'));

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame(401, $response->getStatusCode());
        $this->assertStringContainsString('authentication_required', $response->getBody());
    }

    public function testRejectsApiKeyThatDoesNotMatchSession(): void
    {
        session()->set(['user' => 'Teste', 'user_id' => 10, 'apikey' => 'correta']);
        service('request')->setHeader('APIKEY', 'incorreta');

        $response = (new AiApiAuthFilter())->before(service('request'));

        $this->assertSame(401, $response->getStatusCode());
        $this->assertStringContainsString('invalid_apikey', $response->getBody());
    }

    public function testAcceptsApiKeyThatMatchesAuthenticatedSession(): void
    {
        session()->set(['user' => 'Teste', 'user_id' => 10, 'apikey' => 'correta']);
        service('request')->setHeader('APIKEY', 'correta');

        $this->assertNull((new AiApiAuthFilter())->before(service('request')));
    }
}
