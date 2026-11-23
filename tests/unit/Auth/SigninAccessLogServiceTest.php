<?php

namespace Tests\Unit\Auth;

use App\Models\Auth\SigninAccessLog;
use App\Services\Auth\SigninAccessLogService;
use CodeIgniter\Test\CIUnitTestCase;

final class SigninAccessLogServiceTest extends CIUnitTestCase
{
    public function testRecordsSigninWithoutSensitiveCredentials(): void
    {
        $model = $this->createMock(SigninAccessLog::class);
        $model->expects($this->once())
            ->method('insert')
            ->with($this->callback(static function (array $data): bool {
                return $data['user_id'] === 10
                    && $data['username'] === 'user@example.org'
                    && $data['successful'] === 1
                    && $data['ip_address'] === '203.0.113.10'
                    && $data['user_agent'] === 'Test Agent'
                    && !array_key_exists('password', $data)
                    && !array_key_exists('apikey', $data);
            }))
            ->willReturn(1);

        (new SigninAccessLogService($model))->record(
            true,
            'user@example.org',
            '203.0.113.10',
            '',
            10,
            'Test Agent'
        );
    }

    public function testDatabaseFailureDoesNotEscapeAuthenticationFlow(): void
    {
        $model = $this->createMock(SigninAccessLog::class);
        $model->method('insert')->willThrowException(new \RuntimeException('Database unavailable'));

        (new SigninAccessLogService($model))->record(
            false,
            'user',
            '127.0.0.1',
            'Password is invalid'
        );

        $this->addToAssertionCount(1);
    }
}
