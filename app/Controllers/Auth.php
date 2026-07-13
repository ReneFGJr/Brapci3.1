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

    public function signin()
    {
        $username = trim((string) $this->request->getVar('username'));
        if ($username === '') {
            $username = trim((string) $this->request->getVar('user'));
        }
        if ($username === '') {
            $username = trim((string) $this->request->getVar('email'));
        }

        $password = trim((string) $this->request->getVar('password'));
        if ($password === '') {
            $password = trim((string) $this->request->getVar('pwd'));
        }

        $rsp = [
            'status'  => '400',
            'message' => 'User or Password incorrect',
        ];

        if ($username === '' || $password === '') {
            $rsp['message'] = 'Username or password is empty';
            return $this->response->setJSON($rsp);
        }

        $Socials = new Socials();
        $user = $Socials
            ->groupStart()
            ->where('us_login', $username)
            ->orWhere('us_email', $username)
            ->groupEnd()
            ->first();

        if (!$user) {
            $rsp['message'] = 'User not found';
            return $this->response->setJSON($rsp);
        }

        $storedPassword = (string) ($user['us_password'] ?? '');
        $validPassword = false;

        if ($storedPassword !== '') {
            if ($storedPassword === md5($password)) {
                $validPassword = true;
            } elseif (password_get_info($storedPassword)['algo'] !== 0) {
                $validPassword = password_verify($password, $storedPassword);
            }
        }

        if (!$validPassword) {
            $rsp['message'] = 'Password is invalid';
            return $this->response->setJSON($rsp);
        }

        $apikey = (string) ($user['us_apikey'] ?? '');
        if ($apikey === '') {
            $apikey = md5($storedPassword . ($user['us_email'] ?? ''));
            $Socials->set([
                'us_apikey' => $apikey,
                'us_apikey_active' => 1,
            ])->where('id_us', $user['id_us'])->update();
        }

        $Socials->set([
            'us_lastaccess' => date('Y-m-d H:i:s'),
        ])->where('id_us', $user['id_us'])->update();

        $sessionData = [
            'id'      => $user['id_us'],
            'user'    => $user['us_nome'],
            'email'   => $user['us_email'],
            'apikey'  => $apikey,
            'access'  => substr(md5('#ADMIN'), 6, 6),
            'check'   => substr((string) $user['id_us'] . (string) $user['id_us'], 0, 10),
            'user_id' => $user['id_us'],
        ];

        $_SESSION['id'] = $sessionData['id'];
        $_SESSION['user'] = $sessionData['user'];
        $_SESSION['email'] = $sessionData['email'];
        $_SESSION['apikey'] = $sessionData['apikey'];
        $_SESSION['access'] = $sessionData['access'];
        $_SESSION['check'] = $sessionData['check'];

        session()->set($sessionData);

        $rsp = [
            'status'  => '200',
            'message' => 'Success',
            'user'    => $user['us_nome'],
            'ID'      => $user['id_us'],
            'email'   => $user['us_email'],
            'apikey'  => $apikey,
        ];

        return $this->response->setJSON($rsp);
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

            $recoverLink = base_url('auth/newpass/' . $key);
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

            $emailS = new \App\Models\Functions\Email();

            $result = $emailS->sendmail($email, $subject, $txt);

            $RSP['status'] = 'fase 3 - email';
            $RSP['message'] = 'send_mail';
            $RSP['status'] = '200';
        }

        echo json_encode($RSP);
        exit;
    }

    public function newpass($key = '')
    {
        define('PATH', getenv('app.baseURL') . '/');
        define('URL', getenv('app.baseURL') . '/');
        define('COLLECTION', '/auth');

        $Socials = new Socials();
        $key = trim((string) $key);
        $data['page_title'] = 'Brapci';
        $data['bg'] = 'bg-primary';
        $sx = view('Brapci/Headers/header', $data);
        $sx .= view('Brapci/Headers/navbar', $data);

        $body = '';

        if ($key === '') {
            $body .= bsmessage('Link de recuperação não informado.', 3);
            $sx .= bs(bsc($body, 12));
            $sx .= view('Brapci/Headers/footer', $data);
            return $sx;
        }

        $recover = $Socials->validRecover($key);
        if (!isset($recover['status']) || $recover['status'] !== '200') {
            $body .= bsmessage('Link de recuperação inválido ou expirado.', 3);
            $sx .= bs(bsc($body, 12));
            $sx .= view('Brapci/Headers/footer', $data);
            return $sx;
        }

        session()->set('forgout', $key);

        $pass1 = trim((string) $this->request->getVar('password'));
        $pass2 = trim((string) $this->request->getVar('password_confirm'));

        $body .= '<div class="container py-5">';
        $body .= '<div class="row justify-content-center">';
        $body .= '<div class="col-12 col-md-10 col-lg-6">';
        $body .= '<div class="card border-0 shadow-lg rounded-4 overflow-hidden">';
        $body .= '<div class="card-header bg-white border-0 px-4 pt-4 pb-2">';
        $body .= '<div class="d-flex align-items-center gap-3">';
        $body .= '<div class="rounded-circle d-inline-flex align-items-center justify-content-center bg-primary text-white" style="width: 48px; height: 48px;">';
        $body .= bsicone('lock', 20);
        $body .= '</div>';
        $body .= '<div>';
        $body .= '<h2 class="h4 mb-1">' . lang('social.forgout_new_password') . '</h2>';
        $body .= '<div class="text-muted small">' . lang('social.forgout_password') . '</div>';
        $body .= '</div>';
        $body .= '</div>';
        $body .= '</div>';
        $body .= '<div class="card-body px-4 pb-4">';
        $body .= '<div class="alert alert-info d-flex align-items-start gap-3" role="alert">';
        $body .= '<div class="fw-bold">' . lang('social.forgout_email_user') . '</div>';
        $body .= '<div>' . htmlspecialchars($recover['fullname'] ?? '') . '<br><span class="small text-muted">' . htmlspecialchars($recover['email'] ?? '') . '</span></div>';
        $body .= '</div>';

        if ($this->request->getMethod() === 'post') {
            $result = $Socials->chagePassword($key, $pass1, $pass2);
            if (($result['status'] ?? '') === '200') {
                $body .= bsmessage($result['message'] ?? lang('social.password_changed'), 1);
                $body .= '<br/>';
                $body .= '<a class="btn btn-outline-primary" href="' . base_url('/social/login') . '">' . lang('social.return_login') . '</a>';
                $body .= '</div></div></div></div></div>';
                $sx .= bs(bsc($body, 12));
                $sx .= view('Brapci/Headers/footer', $data);
                return $sx;
            }

            $body .= bsmessage($result['message'] ?? 'Não foi possível alterar a senha.', 3);
        }

        $body .= form_open('/auth/newpass/' . $key, ['method' => 'post', 'class' => 'mt-4']);
        $body .= '<div class="form-group mb-3">';
        $body .= '<label for="password" class="form-label">' . lang('social.forgout_new_password') . '</label>';
        $body .= form_input([
            'name' => 'password',
            'id' => 'password',
            'type' => 'password',
            'class' => 'form-control form-control-lg border border-secondary',
            'value' => $pass1,
            'placeholder' => lang('social.forgout_new_password'),
        ]);
        $body .= '</div>';
        $body .= '<div class="form-group mb-3">';
        $body .= '<label for="password_confirm" class="form-label">' . lang('social.forgout_new_password_confirm') . '</label>';
        $body .= form_input([
            'name' => 'password_confirm',
            'id' => 'password_confirm',
            'type' => 'password',
            'class' => 'form-control form-control-lg border border-secondary',
            'value' => $pass2,
            'placeholder' => lang('social.forgout_new_password_confirm'),
        ]);
        $body .= '</div>';
        $body .= '<div class="d-grid gap-2 mt-4">';
        $body .= form_submit(['class' => 'btn btn-primary btn-lg'], lang('social.save'));
        $body .= '</div>';
        $body .= form_close();
        $body .= '</div>';
        $body .= '</div>';
        $body .= '</div>';
        $body .= '</div>';
        $body .= '</div>';

        $sx .= bs(bsc($body, 12));
        $sx .= view('Brapci/Headers/footer', $data);
        return $sx;
    }
}
