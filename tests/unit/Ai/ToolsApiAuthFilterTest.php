<?php

namespace Tests\Unit\Ai;

use App\Filters\ToolsApiAuthFilter;
use App\Services\Ai\ApiKeyAuthenticator;
use CodeIgniter\Test\CIUnitTestCase;

final class ToolsApiAuthFilterTest extends CIUnitTestCase
{
    protected function tearDown(): void
    {
        session()->destroy();
        parent::tearDown();
    }

    public function testGetRequiresTestFlag(): void
    {
        $authenticator = $this->createMock(ApiKeyAuthenticator::class);
        $authenticator->expects($this->never())->method('findActiveUser');
        service('request')->setMethod('GET');
        service('request')->setGlobal('get', ['APIKEY' => 'key']);

        $response = (new ToolsApiAuthFilter($authenticator))->before(service('request'));

        $this->assertSame(405, $response->getStatusCode());
    }

    public function testGetWithTestFlagRejectsApiKeyFromQueryString(): void
    {
        $authenticator = $this->createMock(ApiKeyAuthenticator::class);
        $authenticator->expects($this->never())->method('findActiveUser');
        service('request')->setMethod('GET');
        service('request')->removeHeader('APIKEY');
        service('request')->setHeader('Origin', 'http://localhost:4200');
        service('request')->setGlobal('get', ['test' => '1', 'APIKEY' => 'pela-query']);
        service('request')->setGlobal('post', []);

        $response = (new ToolsApiAuthFilter($authenticator))->before(service('request'));
        $this->assertSame(401, $response->getStatusCode());
        $this->assertSame(
            'http://localhost:4200',
            service('response')->getHeaderLine('Access-Control-Allow-Origin')
        );
    }
}
