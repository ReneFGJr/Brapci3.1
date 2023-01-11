<?php

namespace App\Models\Base\Admin;

use CodeIgniter\Model;

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
    protected $allowedFields        = [];

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

    function index($act = '', $subact = '', $id ='', $id2 ='', $id3 = '')
    {
        $sx = '';
        switch ($act) {
            case 'qualis':
                $Qualis = new \App\Models\Qualis\Index();
                $sx = $Qualis->index($subact,$id,$id2,$id3);
                break;
            case 'oauth2':
                $Oauth2 = new \App\Models\Oauth2\Index();
                $sx = $Oauth2->OAUTH2();
                break;
            case 'events':
                $Event = new \App\Models\Functions\Event();
                $sx .= $Event->index($subact, $id);
                break;
            case 'email':
                $Email = new \App\Models\Functions\Email();
                $sx .= $Email->test();
                break;
            case 'socials':
                $Socials = new \App\Models\Socials();
                $sx .= $Socials->index($subact, $id);
                break;
            case 'source':
                $Sources = new \App\Models\Base\Sources();
                $sx .= $Sources->index($subact, $id, $id2, $id3);
                break;
            default:
                if (isset($_SESSION['id'])) {
                    $user_name = $_SESSION['id'];
                    $sx .= h(lang('brapci.Hello') . ' ' . $user_name . ' !', 2);
                    $COLLECTION = troca(COLLECTION, '/', '');
                    $sx .= '<h1>' . $COLLECTION . '</h1>';
                    switch ($COLLECTION) {
                        case 'XX':
                            break;
                        default:
                            $sx .= $this->benancib_admin();
                            $sx .= $this->menu();
                            break;
                    }
                    $sx = bs(bsc($sx, 12));
                }
        }
        return $sx;
    }

    function benancib_admin()
    {
        $m['#' . 'benancib.admin.statistics'] =  '';
        $m[PATH .  COLLECTION . '/admin/statistics'] =  lang('benancib.statistics_make');
        $sx = menu($m);
        return $sx;
    }

    function menu()
    {
        $m["#ElasticSearch"] =  "";
        $m[PATH.'elasticsearch'] =  lang("brapci.elasticsearch");

        $m["#Sources"] =  "";
        $m[PATH .  COLLECTION . '/source'] =  lang('brapci.sources');
        $m[PATH .  COLLECTION . '/socials'] =  lang('brapci.Socials');
        $m['#RDF'] =  lang('brapci.rdf');
        $m[PATH .  '/rdf'] =  lang('brapci.rdf');

        $m['#EVENT_CARDS'] =  lang('brapci.event_cards');
        $m[PATH .  '/admin/events'] =  lang('brapci.event_cards');

        $m['#OAUTH2'] =  lang('brapci.event_cards');
        $m[PATH .  '/admin/oauth2'] =  lang('brapci.oauth2');

        $m['#QUALIS'] =  lang('brapci.qualis');
        $m[PATH .  '/admin/qualis'] =  lang('brapci.qualis');

        $m['#CONFIG'] =  lang('brapci.Email');
        $m[PATH .  COLLECTION . '/email'] =  lang('brapci.Email');
        $sx = menu($m);
        return $sx;
    }
}