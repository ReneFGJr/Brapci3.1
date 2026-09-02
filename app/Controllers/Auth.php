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

    private function applySigninCorsHeaders()
    {
        $origin = $this->request->getHeaderLine('Origin');

        $allowed = [
            'https://brapci.inf.br',
            'https://cip.brapci.inf.br',
            'http://localhost:4200',
            'http://localhost',
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

    /***************************************************** |INDEX API| */
    public function index(string $provider)
    {
        $corsResponse = $this->applySigninCorsHeaders();
        if ($corsResponse !== null) {
            return $corsResponse;
        }

        // Call the appropriate method based on the provider
        switch ($provider) {
            /**************** System */
            case 'email':
                $RSP = $this->email();
                break;
            case 'test':
                $RSP = $this->email();
                break;
            /**************** Redirect */
            case 'login':
                return redirect()->to('https://brapci.inf.br/social/signin');
                break;
            /**************** Status */
            case 'status':
                $RSP = $this->status();
                break;
            case 'signin':
                $RSP = $this->signin();
                break;
            case 'signup':
                $RSP = $this->signup();
                break;
            case 'forgot-password':
                $RSP = $this->forgot();
                break;
            case 'forgot':
                $RSP = $this->forgot();
                break;
            default:
                $RSP = [];
                $RSP['status'] = '500';
                $RSP['message'] = 'Unsupported provider';
                break;
        }
        return $this->response->setJSON($RSP);
    }

    public function email()
    {
        $emailS = new \App\Models\Functions\Email();

        return $emailS->test();
    }

    public function status()
    {
        $RSP = [];
        $RSP['status'] = '200';
        $RSP['message'] = 'Authentication is well';
        return $RSP;
    }

    public function __construct()
    {
        helper(['url', 'session', 'sisdoc_email']);
    }

    public function signup()
    {
        $Socials = new Socials();

        return $Socials->signup();
    }

    /***************************************************** |FORGOT API| */
    public function forgot()
    {
        // Aplica CORS e responde ao preflight
        if ($response = $this->applySigninCorsHeaders()) {
            return $response;
        }

        try {

            $Socials = new Socials();

            $email = trim((string) $this->request->getVar('email'));
            $method = strtolower($this->request->getMethod());

            if (!in_array($method, ['get', 'post'], true)) {
                return [
                    'status'  => 405,
                    'message' => 'Method not allowed'
                ];
            }

            if (empty($email)) {
                return [
                    'status'  => 400,
                    'message' => lang('social.email_not_found')
                ];
            }

            $user = $Socials
                ->where('us_email', $email)
                ->first();

            /*
         * Segurança:
         * Não informa se o e-mail existe ou não.
         * Evita enumeração de usuários.
         */
            if (!$user) {
                return [
                    'status'  => 200,
                    'message' => lang('social.email_send_your_account')
                ];
            }

            // Gera token
            $key = $Socials->getRecoverKey($email);

            $Socials
                ->set([
                    'us_recover' => $key
                ])
                ->where('id_us', $user['id_us'])
                ->update();

            session()->set('forgout', $key);

            $recoverLink = base_url('auth/newpass/' . $key);

            $subject = '['
                . getenv('app.project_name')
                . '] '
                . lang('social.forgout_email_title');

            $txt  = '<h1>' . lang('social.forgout_email_title') . '</h1>';
            $txt .= '<center>';
            $txt .= '<table width="600" border="0">';
            $txt .= '<tr>';
            $txt .= '<td><img src="cid:$image1" style="width:100%;"></td>';
            $txt .= '</tr>';
            $txt .= '<tr><td>';

            $txt .= '<br><br>';

            $txt .= '<p style="font-size:1.4em;"><b>';
            $txt .= lang('social.forgout_email_user');
            $txt .= ' ';
            $txt .= esc($user['us_nome']);
            $txt .= '</b></p>';

            $txt .= '<p style="font-size:1.2em;">';
            $txt .= lang('social.forgout_email_text');
            $txt .= '</p>';

            $txt .= '<p style="font-size:1.2em;">';
            $txt .= lang('social.forgout_email_password');
            $txt .= '</p>';

            $txt .= '<p style="font-size:1.2em;">';
            $txt .= '<a href="' . $recoverLink . '">';
            $txt .= $recoverLink;
            $txt .= '</a>';
            $txt .= '</p>';

            $txt .= '<p style="font-size:1.2em;">';
            $txt .= lang('social.forgout_email_text2');
            $txt .= '</p>';

            $txt .= '<p style="font-size:1.2em;">';
            $txt .= lang('social.forgout_email_text3');
            $txt .= '</p>';

            $txt .= '<p style="font-size:1.2em;">';
            $txt .= lang('social.forgout_email_text4');
            $txt .= '</p>';

            $txt .= '</td></tr>';
            $txt .= '</table>';
            $txt .= '</center>';

            $emailS = new \App\Models\Functions\Email();

            $send = $emailS->sendmail(
                $email,
                $subject,
                $txt
            );

            if (!$send) {
                return [
                    'status'  => 500,
                    'message' => 'Erro ao enviar o e-mail.'
                ];
            }

            return [
                'status'  => 200,
                'message' => lang('social.email_send_your_account')
            ];
        } catch (\Throwable $e) {

            log_message('error', $e->getMessage());

            return [
                'status'  => 500,
                'message' => ENVIRONMENT === 'production'
                    ? 'Internal Server Error'
                    : $e->getMessage()
            ];
        }
    }

    /************************************************************* Logout */
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/');
    }

    /************************************************************************* GMAIL */
    public function login_google()
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

    /************************************************************* SIGNIN */
    public function signin()
    {
        $this->applySigninCorsHeaders();

        if (strtoupper((string) $this->request->getMethod()) === 'OPTIONS') {
            return $this->response->setStatusCode(204);
        }


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
            echo json_encode($rsp);
            exit;
            return $this->response->setJSON($rsp);
        }

        echo "OK";
        exit;

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
            return $rsp;
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

        session()->set($sessionData);

        $rsp = [
            'status'  => '200',
            'message' => 'Success',
            'user'    => $user['us_nome'],
            'ID'      => $user['id_us'],
            'email'   => $user['us_email'],
            'givenName' => substr($user['us_nome'], 0, strpos($user['us_nome'], ' ')),
            'token'  => $apikey,
        ];

        return $rsp;
    }

    /************************************************************* getUserInfo */
    private function getUserInfo($accessToken)
    {
        $response = service('curlrequest')->get('https://www.googleapis.com/oauth2/v3/userinfo', [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken
            ]
        ]);

        return json_decode($response->getBody(), true);
    }


    /************************************************************* Forgout */
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

        $method = strtolower($this->request->getMethod());
        if ($method === 'post') {
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
