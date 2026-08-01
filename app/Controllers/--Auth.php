<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\Oauth2\UserModel;
use App\Models\Socials;

helper(['boostrap', 'url', 'sisdoc_forms', 'form', 'nbr', 'sessions', 'cookie']);
$session = \Config\Services::session();

class Auth extends Controller
{
    private $googleClient;

    public function index(string $cmd = '')
    {
        // Aplica os cabeçalhos CORS
        if ($response = $this->applySigninCorsHeaders()) {
            return $response;
        }

        try {

            $Socials = new Socials();

            if (empty($cmd)) {
                $cmd = $this->request->getGet('cmd');
            }

            // Compatibilidade com versão antiga
            if ($cmd === 'forgout') {
                $cmd = 'forgot';
            }

            switch ($cmd) {

                case 'check-change-password':

                    return $this->response->setJSON(
                        $Socials->validRecover(
                            $this->request->getGet('apikey')
                        )
                    );

                case 'test':
                    $emailS = new \App\Models\Functions\Email();

                    $send = $emailS->test();

                    return $this->response->setJSON([
                        'status'  => 200,
                        'message' => 'Teste OK'
                    ]);

                case 'signin':
                    return $this->response->setJSON(
                        $Socials->signin()
                    );

                case 'signup':
                    return $this->response->setJSON(
                        $Socials->signup()
                    );

                case 'forgot':
                    return $this->forgot();

                default:
                    return $this->response
                        ->setStatusCode(404)
                        ->setJSON([
                            'status'  => 404,
                            'message' => "Command '{$cmd}' not found"
                        ]);
            }
        } catch (\Throwable $e) {

            log_message('error', $e->getMessage());

            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'status'  => 500,
                    'message' => 'Internal server error',
                    'error'   => ENVIRONMENT !== 'production'
                        ? $e->getMessage()
                        : null
                ]);
        }
    }

    private function applySigninCorsHeaders()
    {
        $origin = $this->request->getHeaderLine('Origin');

        $allowed = [
            'https://brapci.inf.br',
            'https://cip.brapci.inf.br',
            'http://localhost:4200',
        ];

        if (in_array($origin, $allowed, true)) {
            $this->response->setHeader('Access-Control-Allow-Origin', $origin);
        }

        $this->response->setHeader('Vary', 'Origin');
        $this->response->setHeader('Access-Control-Allow-Credentials', 'true');
        $this->response->setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
        $this->response->setHeader(
            'Access-Control-Allow-Headers',
            'Origin, X-Requested-With, Content-Type, Accept, Authorization'
        );

        if ($this->request->getMethod() === 'OPTIONS') {
            return $this->response->setStatusCode(204);
        }

        return null;
    }

    public function __construct()
    {
        helper(['url', 'session', 'sisdoc_email']);
    }

















}
