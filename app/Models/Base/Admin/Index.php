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
        $sx .h($act.' '.$subact);
        switch ($act) {
            case 'AI':
                $NLP = new \App\Models\AI\NLP\Index();
                $sx .= $NLP->index($subact,$id,$id2);
                break;
            case 'asc':
                $RDFliteral = new \App\Models\RDF2\RDFliteral();
                $sx .= $RDFliteral->ascii();
                break;
            case 'sitemap':
                $SiteMap = new \App\Models\Sitemap\Index();
                $sx .= $SiteMap->index($subact,$id,$id2);
                break;
            case 'pdf':
                $sx .= $this->pdf($subact, $id, $id2, $id3);
                break;
            case 'literal':
                $sx .= $this->literal($subact, $id, $id2, $id3);
                break;
            case 'alias':
                $sx .= $this->alias($subact,$id,$id2,$id3);
                break;
            case 'alias_rdf':
                $sx .= $this->alias_rdf($subact, $id, $id2, $id3);
                break;
            case 'person':
                $sx .= h("Ponto de acesso - Pessoas");
                $Authors = new \App\Models\Base\Authors();
                $sx .= $Authors->form_search();
                $sx .= $Authors->search(get('text'));
                $sx = bs(bsc($sx));
                break;
            case 'corporatebody':
                $sx .= h("Ponto de acesso - Instituições");
                $CorporateBody = new \App\Models\Base\Corporatebody();
                $sx .= $CorporateBody->form_search();
                $sx .= $CorporateBody->search(get('text'));
                $sx = bs(bsc($sx));
                break;
            case 'section':
                $sx .= h("Sections");
                $section = new \App\Models\Base\Sections();
                $sx .= $section->list_not_group();
                $sx .= $section->create_sections();
                break;
            case 'page':
                $WP = new \App\Models\WP\Index();
                $sx .= $WP->index($subact, $id, $id2, $id3);
                break;
            case 'book':
                $Book = new \App\Models\Books\Index();
                $sx .= $Book->index($subact,$id,$id2,$id3);
                break;
            case 'mysql':
                $Mysql = new \App\Models\Io\Mysql();
                $sx .= $Mysql->index($subact,$id,$id2,$id3);
                break;
            case 'task_clear':
                $BOTS = new \App\Models\Bots\Index();

                $BOTS->task_remove($subact);
                $sx .= 'Remove '. $subact;
                $sx .= wclose();
                break;
            case 'issue':
                $sx .= $this->issue($subact,$id,$id2,$id3);
                break;
            case 'problems':
                $Elastic = new \App\Models\ElasticSearch\Index();
                $sx .= $Elastic->index($act, $subact, $id, $id2, $id3);
                break;
            case 'find':
                $Find = new \App\Models\Find\Books\Db\Find();
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
                    $sb = h('Painel',3);

                    $sa = '';
                            $img_mysql = '<img src="'.PATH.'/img/icons/mysql.svg" height="40">';
                            $img_elastic = '<img src="'.PATH.'/img/icons/elasticsearch.png" height="40">';
                            $img_page = '<img src="' . PATH . '/img/icons/ckedit.png" height="40">';
                            $img_sitemap = '<img src="' . PATH . '/img/icons/sitemap.png" height="40">';
                            $sa .= $this->benancib_admin();
                            $sa .= $this->menu();
                            $sb .= '<a title="Bots" href="'.PATH.'/bots/" class="text-success me-2">'.bsicone('android',32).'</a>';
                            $sb .= '<a title="Export" href="' . PATH . 'bots/export/" class="text-success me-2">' . bsicone('upload', 32) . '</a>';
                            $sb .= '<a title="Cron" href="'.PATH.'/admin/cron/" class="text-success me-2">'.bsicone('clock-1',32).'</a>';
                            $sb .= '<a title="Bugs" href="' . PATH . '/admin/bugs/" class="text-success me-2">' . bsicone('bug', 32) . '</a>';
                            $sb .= '<a title="Problems in Export File" href="' . PATH . '/admin/problems/" class="text-success me-2">' . bsicone('maid', 32) . '</a>';
                            $sb .= '<a title="MySQL" href="' . PATH . '/admin/mysql/" class="text-success me-2">' . $img_mysql . '</a>';
                            $sb .= '<a title="ElasticSearch" href="' . PATH . '/elasticsearch/" class="text-success me-2">' . $img_elastic . '</a>';
                            $sb .= '<a title="Content Page" href="' . PATH . '/admin/page/" class="text-success me-2">' . $img_page . '</a>';
                            $sb .= '<a title="SiteMap" href="' . PATH . '/admin/sitemap/" class="text-success me-2">' . $img_sitemap . '</a>';
                            $sb .= $BUGS->resume();
                            $sb .= $this->reports();

                    $sx .= bs(bsc($sa, 6). bsc($sb, 6));
                }
        }
        return $sx;
    }

    function literal($id)
        {
            $sx = '';
            $RDF = new \App\Models\RDF2\RDF();
            $dt = $RDF->le($id);

            if (get("action") != '') {

                $RDFliteral = new \App\Models\RDF2\RDFliteral();
                $dd = [];
                $dd['n_name'] = get("n_name");
                $idn = $dt['concept']['id_n'];
                $RDFliteral->set($dd)
                        ->where('id_n', $idn)
                        ->update();
                $sx = metarefresh(PATH.'admin/alias/'.$id);
                return $sx;
            }

            if ($dt != '')
                {
                    $sx = form_open();
                    $sx .= form_input('n_name',$dt['concept']['n_name'],['class'=>'full']);
                    $sx .= form_submit('action','SAVE');
                    $sx .= form_close();
                }
            return bs(bsc($sx));
        }

    function alias($d1, $idx, $d3, $d4)
    {
        $RDF = new \App\Models\RDF2\RDF();
        $RDFclass = new \App\Models\RDF2\RDFclass();
        $RDFconcept = new \App\Models\RDF2\RDFconcept();

        $t = $RDF->le($d1);
        $class = $t['concept']['c_class'];

        $idc = $RDFclass->getClass($class);

        /***************** Change Class */
        if ($idx == 'change') {
            $idc = $RDFclass->getClass($d3);
            $dd = [];
            $dd['cc_class'] = $idc;
            $RDFconcept->set($dd)->where('id_cc', $d1)->update();
            $idx = 0;
        }

        if (isset($_POST['ids'])) {
            $ids = $_POST['ids'];
            foreach ($ids as $i => $idz) {
                $dd['cc_use'] = $d1;
                $RDFconcept->set($dd)->where('id_cc', $idz)->update();
            }
        }


        $sx = '';
        $dt = $RDF->le($d1);
        $name = $dt['concept']['n_name'];
        $sx .= h($name, 2);
        $sx .= $dt['concept']['c_class'];
        $sx .= '<hr>';
        switch ($dt['concept']['c_class']) {
            case 'Person':
                $sx .= '<a href="' . PATH . 'admin/person">Voltar</a>';
                break;
            case 'CorporateBody':
                $sx .= '<a href="' . PATH . 'admin/corporatebody">Voltar</a>';
                break;
        }

        $sx .= ' | ';
        $sx .= '<a class="btn-danger ms-5" href="' . PATH . 'admin/literal/' . $d1 . '">Editar Literal</a>';
        $sx .= ' | ';
        $sx .= '<a class="btn-danger ms-5" href="' . PATH . 'admin/alias/' . $d1 . '/change/CorporateBody">Mudar Classe [Corporate Body]</a>';
        $sx .= '<hr>';

        $txt = explode(' ', $name);

        $i = 0;
        $sxa = '';
        foreach ($txt as $l) {
            $link = '<a href="' . PATH . 'admin/alias/' . $d1 . '/' . $i . '">';
            $linka = '</a>';
            $sx .= $link . $l . $linka . ' ';
            $i++;
        }
        if ($idx == '') {
            $idx = 0;
        }

        $dt = $RDFconcept
            ->join('brapci_rdf.rdf_literal', 'id_n = cc_pref_term')
            ->like('n_name', $txt[$idx])
            ->where('cc_class', $idc)
            ->where('cc_use = id_cc')
            ->where('id_cc <> ' . $d1)
            ->orderBy('n_name')
            ->findAll(1000);

        $sa = form_open();
        $sb = '';
        foreach ($dt as $id => $line) {
            $nn = $line['n_name'];
            foreach ($txt as $l) {
                $nn = troca($nn, $l, '<b>' . $l . '</b>');
            }
            $sa .= form_checkbox('ids[]', $line['id_cc']) . ' ' . $nn . '<br>';
        }

        $sa .= form_submit('action', 'Join');
        $sa .= form_close();
        $sx = bsc($sx, 12);
        $sx .= bsc($sa, 6);
        $sx .= bsc($sb, 6);
        $sx = bs($sx);
        return $sx;
    }

    function alias_rdf($d1,$idx,$d3,$d4)
        {
            $RDF = new \App\Models\RDF2\RDF();
            $RDFclass = new \App\Models\RDF2\RDFclass();
            $RDFconcept = new \App\Models\RDF2\RDFconcept();

            $t = $RDF->le($d1);
            $class = $idx;

            $idc = $idx;

            if (isset($_POST['ids']))
                {
                    $ids = $_POST['ids'];
                    foreach($ids as $i=>$idz)
                        {
                            $dd['cc_use'] = $d1;
                            $RDFconcept->set($dd)->where('id_cc',$idz)->update();
                        }
                }


            $sx = '';

            /******************************* FILTRO */
            $sx .= form_open();
            $sx .= form_input('filter',get("filter"));
            form_submit('action', 'Filter');
            $sx .= form_close();
            $dt = $RDF->le($d1);
            $name = $dt['concept']['n_name'];
            $sx .= h($name,2);
            $sx .= $dt['concept']['c_class'];
            $sx .= '<hr>';
            switch($dt['concept']['c_class'])
                {
                    case 'Person':
                        $sx .= '<a href="' . PATH . 'admin/person">Voltar</a>';
                        break;
                    case 'CorporateBody':
                        $sx .= '<a href="' . PATH . 'admin/corporatebody">Voltar</a>';
                        break;
                }

            $sx .= ' | ';
            $sx .= '<a class="btn-danger ms-5" href="' . PATH . 'admin/literal/' . $d1 .'">Editar Literal</a>';
            $sx .= '<hr>';

            $txt = explode(' ',$name);

            $i = 0;
            $sxa = '';
            foreach($txt as $l)
                {
                    $link = '<a href="'.PATH.'admin/alias/'.$d1.'/'.$i.'">';
                    $linka = '</a>';
                    $sx .= $link.$l.$linka.' ';
                    $i++;
                }
            if ($idx == '')
                {
                    $idx = 0;
                }

            $dt = $RDFconcept
                ->join('brapci_rdf.rdf_literal', 'id_n = cc_pref_term')
                ->like('n_name', get("filter"))
                ->where('cc_class', $idc)
                ->where('cc_use = id_cc')
                ->where('id_cc <> '.$d1)
                ->orderBy('n_name')
                ->findAll(1000);

            $sa = form_open();
            $sb = '';
            foreach($dt as $id=>$line)
                {
                    $nn = $line['n_name'];
                    foreach($txt as $l)
                        {
                            $nn = troca($nn,$l,'<b>'.$l.'</b>');
                        }
                    $sa .= form_checkbox('ids[]', $line['id_cc']) . ' ' . $nn . '<br>';
                }

            $sa .= form_submit('action','Join');
            $sa .= form_close();
            $sx = bsc($sx,12);
            $sx .= bsc($sa,6);
            $sx .= bsc($sb, 6);
            $sx = bs($sx);
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

                    case 'check2':
                        $sa = bsc(h(lang('brapci.check_year'), 3), 12);
                        $sa .= $ISSUE->check_issues_year();
                        $sx .= bs($sa);
                        break;

                    case 'check3':
                        $sa = bsc(h(lang('brapci.check_issue_type_proceedings'), 3), 12);
                        $sa .= $ISSUE->check_issues_type();
                        $sx .= bs($sa);
                        break;

                    default:
                        $menu['#Check'] = '#';
                        $menu[PATH.'admin/issue/check'] = 'Check ISSUE (Classes)';
                        $menu[PATH . 'admin/issue/check2'] = 'Check ISSUE (Year)';
                        $menu[PATH . 'admin/issue/check3'] = 'Check ISSUE (Processings)';
                        $menu[PATH . 'admin/issue/check4'] = 'Check ISSUE (Journal/Proceedings)';
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
        $m[PATH . '/admin/statistics'] =  lang('benancib.statistics_make');
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
        $m[PATH . 'admin/source'] =  lang('brapci.sources');
        $m[PATH . 'admin/issue'] =  lang('brapci.issue');
        $m[PATH . 'admin/issue_work'] =  lang('brapci.issue_work');
        $m[PATH . '/socials'] =  lang('brapci.Socials');
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
        $m[PATH .  'admin/export'] =  lang('brapci.export');

        $m['#CONFIG'] =  lang('brapci.Email');
        $m[PATH .  'admin/email'] =  lang('brapci.Email');
        $sx = menu($m);
        return $sx;
    }

    function pdf($d1,$d2,$d3)
        {
            $sx = '';

            switch($d1)
                {
                    case 'harvesting':
                        if ($d2 == '') {
                            $d2 = '0';
                        }

                        $Elastic = new \App\Models\ElasticSearch\Register();
                        $sx .= h("OFFSET:".$d2,4);

                        $dt = $Elastic
                            ->select("ID")
                            ->where('PDF',0)
                            ->where('ID > '.$d2)
                            ->where('(CLASS = "Article" or CLASS="Proceeding")')
                            ->orderby("ID")
                            ->findAll(5);

                        foreach($dt as $id=>$line)
                            {
                            $url = 'https://cip.brapci.inf.br//download/'.$line['ID'];
                            $sx .= '<iframe src="'.$url.'" style="width: 100%; height:300px"></iframe>';
                            $offset = $line['ID'];
                            }
                        if (count($dt) > 0)
                            {
                                $sx .= metarefresh(PATH . 'admin/pdf/harvesting/' . $offset, 5);
                            } else {
                                $sx .= h("FIM DA COLETA");
                            }

                }
            return bs(bsc($sx,12));
        }
}