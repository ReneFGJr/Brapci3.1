<?php

namespace App\Filters;

use App\Services\Ai\ApiKeyAuthenticator;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class ToolsApiAuthFilter implements FilterInterface
{
    public function __construct(private ?ApiKeyAuthenticator $authenticator = null)
    {
        $this->authenticator ??= new ApiKeyAuthenticator();
    }

    public function before(RequestInterface $request, $arguments = null)
    {
        $method = strtoupper((string) $request->getMethod());
        if ($method === 'GET' && (string) $request->getGet('test') !== '1') {
            return service('response')->setStatusCode(405)->setJSON([
                'error' => 'method_not_allowed',
                'message' => 'Use POST. GET e permitido somente com test=1.',
            ]);
        }

        if (!in_array($method, ['GET', 'POST'], true)) {
            return service('response')->setStatusCode(405)->setJSON([
                'error' => 'method_not_allowed',
                'message' => 'Metodo nao permitido.',
            ]);
        }

        $headerApiKey = trim($request->getHeaderLine('APIKEY'));
        $queryApiKey = trim((string) $request->getGet('APIKEY'));
        $postApiKey = trim((string) $request->getPost('APIKEY'));
        $json = $request->getJSON(true);
        $jsonApiKey = is_array($json) ? trim((string) ($json['APIKEY'] ?? '')) : '';
        $apiKey = $headerApiKey !== ''
            ? $headerApiKey
            : ($queryApiKey !== ''
                ? $queryApiKey
                : ($postApiKey !== '' ? $postApiKey : $jsonApiKey));

        if ($apiKey === '') {
            return service('response')->setStatusCode(401)->setJSON([
                'error' => 'authentication_required',
                'message' => 'APIKEY nao informada.',
            ]);
        }

        $user = $this->authenticator->findActiveUser($apiKey);
        if ($user === null) {
            return service('response')->setStatusCode(401)->setJSON([
                'error' => 'invalid_apikey',
                'message' => 'APIKEY invalida ou inativa.',
            ]);
        }

        session()->set([
            'id' => $user['id_us'],
            'user_id' => $user['id_us'],
            'user' => $user['us_nome'],
            'email' => $user['us_email'],
            'apikey' => $user['us_apikey'],
        ]);
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
