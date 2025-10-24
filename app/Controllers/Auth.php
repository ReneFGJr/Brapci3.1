<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\Oauth2\UserModel;

helper(['boostrap', 'url', 'sisdoc_forms', 'form', 'nbr', 'sessions', 'cookie']);
$session = \Config\Services::session();

class Auth extends Controller
{
    private $googleClient;

    public function __construct()
    {
        helper(['url', 'session']);
    }

    public function login()
    {
        $client_id = getenv('google.client_id');
        $redirect_uri = getenv('google.redirect_uri');

        $scope = urlencode('email profile');
        $state = bin2hex(random_bytes(8));

        session()->set('oauth_state', $state);

        $url = "https://accounts.google.com/o/oauth2/v2/auth?" . http_build_query([
            'client_id'     => $client_id,
            'redirect_uri'  => $redirect_uri,
            'response_type' => 'code',
            'scope'         => 'openid email profile',
            'state'         => $state,
            'access_type'   => 'offline',
            'prompt'        => 'select_account'
        ]);
        return redirect()->to($url);
    }

    public function status()
        {
            pre($_SESSION,false);
            $userData = $_SESSION['userOAUTH2'];
            $Socials = new \App\Models\Socials();
            $Socials->OAUTH2_user($userData);
    }

    public function callback()
    {
        $state = $this->request->getVar('state');
        $sessionState = session()->get('oauth_state');

        /*
        if (!$state || $state !== $sessionState) {
            echo "Invalid state.";
            exit;
            return redirect()->to('/')->with('error', 'Invalid state.');
        }
        */

        $code = $this->request->getVar('code');
        if (!$code) {
            echo "Authorization code missing.";
            exit;
            return redirect()->to('/')->with('error', 'Authorization code missing.');
        }

        // Troca o código por token
        $tokenData = $this->getAccessToken($code);
        if (isset($tokenData['error'])) {
            echo "Failed to obtain token.";
            exit;
            return redirect()->to('/')->with('error', 'Failed to obtain token.');
        }

        // Obter dados do usuário
        $userData = $this->getUserInfo($tokenData['access_token']);
        $userData['type'] = 'google';

        $_SESSION['userOAUTH2'] = $userData;

        $Socials = new \App\Models\Socials();
        $Socials->OAUTH2_user($userData);

        // Salvar ou atualizar no banco
        pre($userData);


        return redirect()->to('/dashboard');
    }

    private function getAccessToken($code)
    {
        $url = "https://oauth2.googleapis.com/token";

        $response = service('curlrequest')->post($url, [
            'form_params' => [
                'code'          => $code,
                'client_id'     => getenv('google.client_id'),
                'client_secret' => getenv('google.client_secret'),
                'redirect_uri'  => getenv('google.redirect_uri'),
                'grant_type'    => 'authorization_code',
            ]
        ]);

        return json_decode($response->getBody(), true);
    }

    private function getUserInfo($accessToken)
    {
        $response = service('curlrequest')->get('https://www.googleapis.com/oauth2/v3/userinfo', [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken
            ]
        ]);

        return json_decode($response->getBody(), true);
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/');
    }
}
