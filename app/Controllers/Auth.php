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
            $userData = $_SESSION['userOAUTH2'];
            $Socials = new \App\Models\Socials();
            echo $Socials->OAUTH2_user($userData);
    }

    /**
     * Etapa 2 – Callback do Google
     */
    public function callback()
    {
        $state         = $this->request->getVar('state');
        $sessionState  = session()->get('oauth_state');

        // ✅ validação do state
        if (!$state || $state !== $sessionState) {
            session()->remove('oauth_state');
            return redirect()->to('/')->with('error', 'Invalid state.');
        }

        session()->remove('oauth_state'); // remove após uso

        $code = $this->request->getVar('code');
        if (!$code) {
            return redirect()->to('/')->with('error', 'Authorization code missing.');
        }

        // 🔄 troca code por token
        $tokenData = $this->getAccessToken($code);
        if (!isset($tokenData['access_token'])) {
            return redirect()->to('/')->with('error', 'Failed to obtain token.');
        }

        // 👤 obtém dados do usuário
        $userData = $this->getUserInfo($tokenData['access_token']);
        $userData['type'] = 'google';

        $_SESSION['userOAUTH2'] = $userData;

        $Socials = new Socials();
        $token = $Socials->OAUTH2_user($userData);

        if (!$token) {
            return redirect()->to('/')->with('error', 'Error processing user data.');
        }

        return redirect()->to('https://brapci.inf.br/callback2/' . $token);
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
