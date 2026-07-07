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
        helper(['url', 'session', 'sisdoc_email']);
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
            $token = $Socials->OAUTH2_user($userData);
            if (!$token) {
                echo "Não logado";
                exit;
            } else {
                echo 'http://localhost:4200/callback/' . $token;
            }
            exit;
            return redirect()->to('https://brapci.inf.br/callback/' . $token);
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

        return redirect()->to('https://brapci.inf.br/callback/' . $token);
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

    public function forgot()
    {
        $Socials = new Socials();
        $sx = '';

        $email = trim((string) $this->request->getVar('email'));
        $method = strtolower($this->request->getMethod());
        $RSP = [];
        $RSP['email'] = $email;
        $RSP['method'] = $method;

        if (($method === 'post') or ($method === 'get')) {
            $RSP['status'] = 'fase 1';
            if ($email === '') {
                $RSP['error'] = lang('social.email_not_found');
            }

            $user = $Socials->where('us_email', $email)->first();
            if (!$user) {
                $RSP['error'] = lang('social.email_not_found');
            }

            $key = $Socials->getRecoverKey($email);
            $Socials->set(['us_recover' => $key])->where('id_us', $user['id_us'])->update();
            $RSP['status'] = 'fase 2';
            session()->set('forgout', $key);

            $recoverLink = base_url('social/pass/' . $key);
            $subject = '[' . getenv('app.project_name') . '] ' . lang('social.forgout_email_title');

            $txt = '<h1>' . lang('social.forgout_email_title') . '</h1>';
            $txt .= '<center>';
            $txt .= '<table width="600" border="0">';
            $txt .= '<tr><td><img src="cid:$image1" style="width: 100%;"></td></tr>';
            $txt .= '<tr><td cellpadding="5">';
            $txt .= '<br/><br/>';
            $txt .= '<p style="font-size: 1.4em;"><b>' . lang('social.forgout_email_user') . ' ' . $user['us_nome'] . '</b></p>';
            $txt .= '<p style="font-size: 1.2em;">' . lang('social.forgout_email_text') . '</p>';
            $txt .= '<p style="font-size: 1.2em;">' . lang('social.forgout_email_password') . '</p>';
            $txt .= '<p style="font-size: 1.2em;"><a href="' . $recoverLink . '">' . $recoverLink . '</a></p>';
            $txt .= '<p style="font-size: 1.2em;">' . lang('social.forgout_email_text2') . '</p>';
            $txt .= '<p style="font-size: 1.2em;">' . lang('social.forgout_email_text3') . '</p>';
            $txt .= '<p style="font-size: 1.2em;">' . lang('social.forgout_email_text4') . '</p>';
            $txt .= '</td></tr>';
            $txt .= '</table>';
            $txt .= '</center>';

            $result = sendmail($email, $subject, $txt);
            $RSP['status'] = 'fase 3 - email';
            $RSP['message'] = 'send_mail';
            $RSP['status'] = '200';
        }

        echo json_encode($RSP);
        exit;
    }
}
