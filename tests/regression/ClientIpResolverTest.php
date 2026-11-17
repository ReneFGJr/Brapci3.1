<?php
// Executar: php tests/regression/ClientIpResolverTest.php

require __DIR__ . '/../../app/Services/Auth/ClientIpResolver.php';

use App\Services\Auth\ClientIpResolver;

function check(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

check(
    ClientIpResolver::resolve('203.0.113.20, 10.0.0.5', '', '10.0.0.10') === '203.0.113.20',
    'Usar o primeiro IP valido de X-Forwarded-For'
);
check(
    ClientIpResolver::resolve('invalido', '198.51.100.9', '10.0.0.10') === '198.51.100.9',
    'Usar X-Real-IP quando X-Forwarded-For nao for valido'
);
check(
    ClientIpResolver::resolve('', '', '192.0.2.7') === '192.0.2.7',
    'Manter o IP da conexao como fallback'
);
check(
    ClientIpResolver::resolve('texto, 2001:db8::1', '', '192.0.2.7') === '2001:db8::1',
    'Aceitar IPv6 encaminhado valido'
);

echo "OK: IP original do cliente resolvido a partir dos cabecalhos do proxy.\n";
