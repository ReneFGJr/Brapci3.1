<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AiApiAuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $sessionApiKey = trim((string) session()->get('apikey'));
        $headerApiKey = trim($request->getHeaderLine('APIKEY'));

        if (! session()->get('user') || ! session()->get('user_id') || $sessionApiKey === '') {
            return service('response')->setStatusCode(401)->setJSON([
                'error' => 'authentication_required',
                'message' => 'Sua sessao expirou. Entre novamente.',
            ]);
        }

        if ($headerApiKey === '' || ! hash_equals($sessionApiKey, $headerApiKey)) {
            return service('response')->setStatusCode(401)->setJSON([
                'error' => 'invalid_apikey',
                'message' => 'APIKEY ausente ou invalida.',
            ]);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
