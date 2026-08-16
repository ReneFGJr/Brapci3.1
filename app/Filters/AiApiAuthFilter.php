<?php

namespace App\Filters;

use App\Services\Ai\ApiKeyAuthenticator;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AiApiAuthFilter implements FilterInterface
{
    public function __construct(private ?ApiKeyAuthenticator $authenticator = null)
    {
        $this->authenticator ??= new ApiKeyAuthenticator();
    }

    public function before(RequestInterface $request, $arguments = null)
    {
        $headerApiKey = trim($request->getHeaderLine('APIKEY'));

        if ($headerApiKey === '') {
            return service('response')->setStatusCode(401)->setJSON([
                'error' => 'authentication_required',
                'message' => 'APIKEY nao informada - API/AI.',
            ]);
        }

        $user = $this->authenticator->findActiveUser($headerApiKey);
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
