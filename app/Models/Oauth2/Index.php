<?php

namespace App\Models\Oauth2;

use CodeIgniter\Model;

use Google\Auth\CredentialsLoader;
use Google\Auth\ApplicationDefaultCredentials;
use Google\Auth\Middleware\AuthTokenMiddleware;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;

class Index extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'indices';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    function oauth2_feedback()
        {
            pre($_POST,false);
            pre($_GET,false);
            exit;
        }

    function OAUTH2()
        {
            /* https://www.youtube.com/watch?v=VDBJMS4zl8I */
            require_once APPPATH.'Libraries/vendor/autoload.php';
            $google_ID = getenv("GoogleID");
            $google_PW = getenv("GoodleSecret");

            $google_client = new \Google_Client();
            $google_client->setClientID($google_ID);
            $google_client->setClientSecret($google_PW);
            $google_client->setRedirectUri(PATH.'social/oauth2/?cmd=oauth2&');
            $google_client->addScope('email');
            $google_client->addScope('profile');

            $data['loginGoofle'] = $google_client->createAuthUrl();

            $sx = '<a href="'.$data['loginGoofle'].'">Login with Google</a>';
            echo $sx;
            return $sx;

        }
}
