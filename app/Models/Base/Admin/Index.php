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
            case 'issue':
                $sx .= $this->issue($subact,$id,$id2,$id3);
                break;
            case 'find':
                $Find = new \App\Models\Find\Index();
                $sx .= $Find->index($subact, $id, $id2, $id3);
                break;
            case 'vc':
                $Thesaurus = new \App\Models\ControlledVocabulary\Thesaurus();
                $sx .= $Thesaurus->index($subact, $id, $id2, $id3);
                break;
            case 'cron':
                $Cron = new \App\Models\Bots\Cron();
                $sx .= $Cron->index($subact,$id,$id2,$id3);
                break;
            case 'dataset':
                $API = new \App\Models\ElasticSearch\Index();
                $sx .= $API->show_error($subact,$id,$id2,$id3);
                break;
            case 'elastic':
                $API = new \App\Models\ElasticSearch\Index();
                $sx .= bs(bsc($API->index('update_index'),12));
                break;
            case 'reports':
                $Reports = new \App\Models\Base\Admin\Reports();
                $sx .= $Reports->index($subact,$id,$id2,$id3);
                break;
            case 'cache':
                $Cache = new \App\Models\Functions\Cache();
                $sx = $Cache->index();
                break;
            case 'lattes':
                $Lattes = new \App\Models\Api\Lattes\Index();
                $sx .= $Lattes->index($subact, $id, $id2);
                break;
            case 'bugs':
                $Bugs = new \App\Models\Functions\Bugs();
                $sx = $Bugs->index($subact, $id, $id2, $id3);
                break;
            case 'manegement':
                $Manegement = new \App\Models\Base\Manegement();
                $sx = $Manegement->index($subact, $id, $id2, $id3);
                break;
            case 'export':
                $Export = new \App\Models\Base\Export();
                $sx = $Export->index($subact, $id, $id2, $id3);
                break;
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
                    $Socials = new \App\Models\Socials();
                    $BUGS = new \App\Models\Functions\Bugs();

                    $Manegement = new \App\Models\Base\Manegement();
                    $sx = $Manegement->index($subact, $id, $id2, $id3);

                    $user_id = $_SESSION['id'];
                    $usd = $Socials->find($user_id);
                    $user_name = $usd['us_nome'];
                    $sx .= bs(bsc(h(lang('brapci.Hello') . ' ' . $user_name . ' !', 2),12));
                    $COLLECTION = troca(COLLECTION, '/', '');
                    $sa = h($COLLECTION,3);
                    $sb = h('Painel',3);
                    switch ($COLLECTION) {
                        case 'XX':
                            break;
                        default:
                            $sa .= $this->benancib_admin();
                            $sa .= $this->menu();
                            $sb .= '<a title="Bots" href="'.PATH.'/bots/" class="text-success me-2">'.bsicone('android',32).'</a>';
                            $sb .= '<a title="Export" href="' . PATH . '/admin/export/" class="text-success me-2">' . bsicone('upload', 32) . '</a>';
                            $sb .= '<a title="Cron" href="'.PATH.'/admin/cron/" class="text-success me-2">'.bsicone('clock-1',32).'</a>';
                            $sb .= '<a title="Bugs" href="' . PATH . '/admin/bugs/" class="text-success me-2">' . bsicone('bug', 32) . '</a>';
                            $sb .= $BUGS->resume();
                            $sb .= $this->reports();
                            break;
                    }
                    $sx .= bs(bsc($sa, 6). bsc($sb, 6));
                }
        }
        return $sx;
    }

    function issue($d1,$d2,$d3)
        {
            $ISSUE = new \App\Models\Base\Issues();
            $RDF = new \App\Models\Rdf\RDF();
            $sx = h('ISSUE');

            $sx = bs(bsc($sx,12));
            switch($d1)
                {
                    case 'check':
                        $sa = bsc(h(lang('brapci.check'),3),12);
                        $sa .= $ISSUE->check_issues();
                        $sx .= bs($sa);
                    break;

                    default:
                        $menu['#Check'] = '#';
                        $menu[PATH.'/admin/issue/check'] = 'Check ISSUE';
                        $sx .= bs(bsc(menu($menu),12));
                        break;
                }
            return $sx;
        }

    function reports()
        {
            $sx = h(lang('brapci.reports'),5);
            $sx .= '<ul>';
            $opt = array('catalog_manutention');
            foreach($opt as $id=>$type)
            {
                $link = '<a href="'.PATH.'/admin/reports/'.$type.'">';
                $linka = '</a>';
                $sx .= '<li>'.$link.lang('brapci.'.$type).$linka.'</li>';
            }
            $sx .= '</ul>';
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

        $m["#FIND"] =  "";
        $m[PATH . 'admin/find'] =  lang('brapci.find');

        $m["#Manegement"] =  "";
        $m[PATH . 'admin/manegement'] =  lang('brapci.manegement');
        $m[PATH . 'admin/vc'] =  bsicone('vc',32).' '.lang('brapci.vc');

        $m["#Sources"] =  "";
        $m[PATH .  COLLECTION . '/source'] =  lang('brapci.sources');
        $m[PATH .  COLLECTION . '/issue'] =  lang('brapci.issue');
        $m[PATH .  COLLECTION . '/issue_work'] =  lang('brapci.issue_work');
        $m[PATH .  COLLECTION . '/socials'] =  lang('brapci.Socials');
        $m['#RDF'] =  lang('brapci.rdf');
        $m[PATH .  '/rdf'] =  lang('brapci.rdf');

        $m['#BUGS'] =  lang('brapci.Bugs');
        $m[PATH .  '/admin/bugs'] =  lang('brapci.Bugs');
        $m[PATH .  '/admin/cache'] =  lang('brapci.Cache');

        $m['#EVENT_CARDS'] =  lang('brapci.event_cards');
        $m[PATH .  '/admin/events'] =  lang('brapci.event_cards');

        $m['#OAUTH2'] =  lang('brapci.event_cards');
        $m[PATH .  '/admin/oauth2'] =  lang('brapci.oauth2');

        $m['#QUALIS'] =  lang('brapci.qualis');
        $m[PATH .  '/admin/qualis'] =  lang('brapci.qualis');

        $m['#EXPORT'] =  lang('brapci.export');
        $m[PATH .  COLLECTION . '/export'] =  lang('brapci.export');

        $m['#CONFIG'] =  lang('brapci.Email');
        $m[PATH .  COLLECTION . '/email'] =  lang('brapci.Email');
        $sx = menu($m);
        return $sx;
    }
}