<?php

namespace App\Models\Base;

use CodeIgniter\Model;

class Sources extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'source_source';
    protected $primaryKey       = 'id_jnl';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    public $allowedFields        = [
        'id_jnl', 'jnl_name', 'jnl_name_abrev',
        'jnl_issn', 'jnl_eissn', 'jnl_periodicidade',
        'jnl_ano_inicio', 'jnl_ano_final', 'jnl_url',
        'jnl_url_oai', 'jnl_oai_from', 'jnl_cidade',
        'jnl_scielo', 'jnl_collection', 'jnl_active',
        'jnl_historic', 'jnl_frbr'
    ];

    protected $viewFields        = [
        'id_jnl', 'jnl_name', 'jnl_name_abrev',
        'jnl_issn'
    ];

    protected $typeFields        = [
        'hidden', 'string:100:#', 'string:20:#',
        'string:20:#', 'string:20', 'op: & :Q&Quadrimestral:S&Semestral:A&Anual:F&Continuos FLuxo',
        'year', 'year', 'string:20',
        'string:20', 'string:20', 'string:20',
        'sn', 'string:20', 'sn',
        'sn', 'string:20'
    ];

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
    var $path = '';
    var $path_back = '';
    var $id = 0;

    function index($d1, $d2, $d3)
    {
        $this->path = base_url(PATH . MODULE . '/index/');
        $this->path_back = base_url(PATH . MODULE . '/index/');

        switch ($d1) {
                /******************* Validade ******/
            default:
                $sx = $this->menu();
                break;

            case 'view':
                    $sx = $this->view($d2);
                break;

            case 'list':
                switch($d2)
                    {
                        case '0':
                            $sx = $this->list();
                            break;
                        case '1':
                            $sx = $this->list();
                            break;
                        case '2':
                            $sx = $this->list('b');
                            break;
                        default:
                        break;
                    }

                break;

            case 'check':
                $sx = $this->check($d2,$d3);
                break;

            case 'menu':
                $sx = $this->menu();
                break;

            case 'tableview':
                $this->path = PATH.'admin/source';
                $this->path_back = PATH. 'admin/source/tableview';
                $sx = bs(bsc(tableview($this,$_POST)));
                break;

            /******************* Implementando */
            case 'issue':
                $sx = $this->issue($d1, $d2, $d3);
                break;

            case 'harvesting':
                $sx = 'Harvesting';
                break;

            /******************* Para testes ***/
            case 'edit_issue':
                $sx = $this->editar_issue($d2, $d3);
                break;
            case 'oai_check':
                $sx = $this->oai_check();
                break;
            case 'edit':
                $d2 = round('0'.trim($d2));
                $sx = $this->editar($d2);
                break;
            case 'viewid':
                $sx = $this->viewid($d2);
                break;
            case 'view_issue':
                $sx = $this->view_issue_id($d2);
                break;
            case 'oai':
                $sx = $this->oai($d2, $d3);
                break;
            case 'edit':
                $sx = $this->editar($d2);
                break;
        }
        return $sx;
    }

    function getCollections()
        {
            $c = [];
            $dt = $this->findAll();
            foreach($dt as $id=>$line)
                {
                    $col = $line['jnl_collection'];
                    switch($col)
                        {
                            case 'JA':
                                $col = 'Revista Brasileira';
                                break;
                            case 'JE':
                                $col = 'Revistas Estrangeira';
                                break;
                            case 'EV':
                                $col = 'Anais de evento';
                                break;
                        }
                    $c[$line['id_jnl']] = $col;
                }
            return $c;
        }

    function check($id,$id2)
        {
            $Issue = new \App\Models\Base\Issues();
            $sx = $Issue->check($id,$id2);
            return $sx;
        }

    function list_json()
        {
            $cp = 'id_jnl, jnl_name, jnl_name_abrev, jnl_issn, jnl_eissn, jnl_ano_inicio, jnl_ano_final, jnl_historic, jnl_active, jnl_frbr';
            $dt = $this
            ->select($cp)
            ->OrderBy('jnl_name')
            ->findAll();
            return json_encode($dt);
        }

    function view($id)
        {
            $dt = $this->where('id_jnl',$id)->first();

            $sa = '';
            $sb = '';
            $sx = '';
            $sa .= h($dt['jnl_name'],3);
            $sa .= h($dt['jnl_name_abrev'], 5);
            $sa .= h('ISSN: ' . $dt['jnl_issn'],6);
            $sa .= h('eISSN: ' . $dt['jnl_eissn'], 6);
            $sa .= h('Periodicidade: ' . $dt['jnl_periodicidade'], 6);
            $sa .= h('Ano Início: ' . $dt['jnl_ano_inicio'], 6);
            $sa .= h('Ano Final: ' . $dt['jnl_ano_final'], 6);
            $sa .= h('URL: ' . anchor($dt['jnl_url']), 6);
            $sa .= h('URL OAI: '.anchor($dt['jnl_url_oai']), 6);
            $sa .= h($dt['jnl_oai_last_harvesting'], 6);
            $sa .= h('Updated: ' . stodbr($dt['update_at']), 6);

            $sa .= h('Status: ' . $dt['jnl_oai_status'], 6);
            $sa .= h($dt['jnl_oai_to_harvesting'], 6);
            $sa .= h('Collection: '.$dt['jnl_collection'], 6);
            $sa .= h('Historic: ' . $dt['jnl_historic'], 6);
            $sa .= h('RDF: ' . $dt['jnl_frbr'], 6);


            $sx .= bsc($sa,10);
            $sx .= bsc($sb,2);

            $sx = bs($sx);
            return $sx;
        }

    function list($type='')
        {
            if ($type=='json')
                {
                    return $this->list_json();
                }
            $Socials = new \App\Models\Socials();
            $sx = '';
            switch($type)
                {
                    case 'b':
                        $fld = 'year';
                        $fldo = $fld.' DESC';
                        break;
                    default:
                        $fld = 'jnl_name';
                        $fldo = $fld;
                        break;
                }

            $dt = $this
                ->join('(SELECT `is_source`, max(is_year) as year FROM `source_issue` GROUP BY `is_source`) as issues', 'is_source = id_jnl', 'left')
                ->where('jnl_collection','JA')
                ->ORwhere('jnl_collection', 'JE')
                ->OrderBy($fldo)
                ->findAll();

            $xlb = '';

            $stx = [];
            $tt = 0;

            foreach($dt as $id=>$line)
                {
                    $lb = $line[$fld];
                    if (($type != '') and ($xlb != $lb))
                        {
                            $xlb = $lb;
                            $sx .= h($xlb,4);
                        }
                    $link = anchor(PATH . '/journals/view/' . $line['id_jnl'], $line['jnl_name']);
                    $tt++;
                    $sx .= bsc($tt.'. '.$link,9);

                    $link = '';
                    if ($Socials->perfil('#ADM'))
                        {
                            //$link = anchor(PATH . '/journals/check/' . $line['jnl_frbr'], '(check)', 'target="_black" ');
                        }

                    $sx .= bsc('',1);
                    $sx .= bsc($line['year'], 1);
                    $sta = $line['jnl_oai_status'];

                    if (isset($stx[$sta]))
                        {
                            $stx[$sta] = $stx[$sta] + 1;
                        } else {
                            $stx[$sta] = 1;
                        }

                    switch($sta)
                        {
                            case '100';
                                $sta = '<span class="btn btn-success p-0 full">OK</span>';
                                break;
                            case '200';
                                $sta = '<span class="btn btn-secondary p-0 ful small">HISTORICA</span>';
                                break;
                            case '510';
                                $sta = '<span class="btn btn-danger p-0 full">ERRO 510</span>';
                                break;
                            case '501';
                                $sta = '<span class="btn btn-warning p-0 full">ERRO 501</span>';
                                break;
                            case '404';
                                $sta = '<span class="btn btn-danger p-0 full small">ERRO 404</span>';
                                break;
                        }
                    $sx .= bsc($sta, 1);
                }

            $sx .= '<li>404 - Page not found</li>';
            $sx .= '<li>501 - Content empty</li>';
            $sx .= '<li>510 - Process Content error</li>';

            $sx = bs($sx);

            /************ Painel */
            $sp = '<table class="table full"><tr>';
            foreach($stx as $s=>$total)
                {
                    $sp .= '<td>'.($s.'<br><span class="h4">'.$total. '</span>'). '</td>';
                }
            $sp .= '</tr></table>';
            $sx = bs($sp) . $sx;
            //pre($dt);
            return $sx;
        }


    function source_list_block($type='EV')
        {
            $dt = $this->where('jnl_collection',$type)->orderBy('jnl_name_abrev')->findAll();
            $sx = '';
            for ($r=0; $r < count($dt); $r++)
                {
                    $line = $dt[$r];

                    $link = '<a href="'. PATH. COLLECTION . '/source/'. $line['id_jnl'] . '" class="text-secondary">';
                    $linka = '</a>';

                    $sa = '';
                    $sa .= $link;
                    $sa .= h($line['jnl_name_abrev'], 2);
                    $sa .= $line['jnl_name'];
                    $sa .= $linka;

                    $sx .= bsc($sa,4,'text-center border border-primary p-2');
                    //pre($line);
                }
            $sx = bs($sx);
            return $sx;

        }

    function list_selected()
    {
        if (!isset($_SESSION['sj'])) {
            $sj = array();
        } else {
            $sj = (array)json_decode($_SESSION['sj']);
        }
        $lst = '';
        $max = 15;
        $nr = 0;
        $sx = '';
        $more = 0;
        foreach ($sj as $jid => $active)
            if ($active == 1) {
                $dt = $this->find($jid);
                if ($nr < $max) {
                    if (strlen($sx) > 0) {
                        $sx .= '; ';
                    }
                    $sx .= $dt['jnl_name_abrev'];
                    $nr++;
                } else {
                    $more++;
                }
            }
        if ($more > 0) {
            $sx .= lang('brapci.more') . ' +' . ($more);
        }
        if ($sx == '') {
            $sx = lang('brapci.select_sources') . ' ' . bsicone('folder-1');
        } else {
            $sx .= '.';
        }
        return $sx;
    }

    function ajax()
    {
        $id = get("id");
        $ok = get("ok");
        if (!isset($_SESSION['sj'])) {
            $sj = array();
        } else {
            $sj = (array)json_decode($_SESSION['sj']);
        }

        /********************************* CHECK */
        if (!isset($sj[$id])) {
            $sj[$id] = 1;
        } else {
            if ($sj[$id] == 1) {
                $sj[$id] = 0;
            } else {
                $sj[$id] = 1;
            }
        }
        $_SESSION['sj'] = json_encode($sj);

        return $this->list_selected();
    }

    function search_source()
    {
        if (isset($_SESSION['sj'])) {
            $sj = (array)json_decode($_SESSION['sj']);
        } else {
            $sj = array();
        }


        $dt = $this
            ->orderBy("jnl_collection, jnl_name")
            ->FindAll();
        $sx = '';

        $xcollection = '';
        $sx .= '<ul style="list-style-type: none;">';
        for ($r = 0; $r < count($dt); $r++) {
            $line = $dt[$r];
            $id = $line['id_jnl'];

            $check = '';
            if (isset($sj[$id])) {
                if ($sj[$id] == 1) {
                    $check = 'checked';
                }
            }
            $collection = trim($line['jnl_collection']);
            if ($collection != $xcollection) {
                $xcollection = $collection;
                $sx .= h(lang('brapci.' . $collection), 4);
            }
            $sx .= '<li>';
            $sx .= '<input type="checkbox" id="jnl_' . $id . '" ' . $check . ' class="me-2" onclick="markSource(' . $id . ',this);">';
            $sx .= $line['jnl_name'];
            if (strlen(trim($line['jnl_issn'])) > 0) {
                $sx .= ' (ISSN ' . $line['jnl_issn'] . ')';
            }
            $sx .= '</>';
        }
        $sx .= '</ul>';
        return $sx;
    }


    function menu()
    {
        $Socials = new \App\Models\Socials();
        $access = $Socials->getAccess('#ADM#CAT');
        $sx = '';
        $items = array();
        $mod = '/source';


        $items['/journals/list/0'] = lang('brapci.sources');

        $items['/journals/list/1'] = lang('brapci.sources');
        $items['/journals/list/2'] = lang('brapci.sources.lastpublications');
        if ($access)
            {
                $items['/admin' . $mod . '/tableview'] = 'TableView';
            }

        $sb = menu($items);
        $sa = $this->resume();

        $sx = bs(bsc($sa,4).bsc($sb,6));
        return $sx;
    }

    /******************************************** RESUME */
    function resume()
    {
        $sx = h(lang('brapci.sources'),4);
        $dt = $this->select('count(*) as total, jnl_collection, jnl_historic')
            ->groupBy('jnl_collection, jnl_historic')
            ->findAll();
        $total = 0;
        $types = array();
        $historic = array();
        $tot = 0;
        for ($r=0;$r < count($dt);$r++)
            {
                $line = $dt[$r];
                $type = $line['jnl_collection'];
                $hist = $line['jnl_historic'];

                if (!isset($types[$type])) $types[$type] = 0;
                if (!isset($historic[$hist])) $historic[$hist] = 0;

                $types[$type] = $types[$type] + $line['total'];
                $historic[$hist] = $historic[$hist] + $line['total'];
                $tot = $tot + $line['total'];
            }
        $sx .= '<b style="font-size: 0.7em;">Total ' . $tot . '</b>';
        $sx .= '<ul style="font-size: 0.7em;">';
        foreach($types as $type=>$total)
            {
                $sx .= '<li>'.lang('brapci.source_type.'.$type).' ('.$total.')</li>';
            }
        $sx .= '</ul>';

        $sx .= '<ul style="font-size: 0.7em;">';
        foreach ($historic as $type => $total) {
            $sx .= '<li>' . lang('brapci.source_historic.' . $type) . ' (' . $total . ')</li>';
        }
        $sx .= '</ul>';


        return $sx;
    }

    function le_rdf($id)
    {
        $dt = $this->where('jnl_frbr', $id)->FindAll();
        return $dt;
    }

    function issn($dt)
    {
        //https://portal.issn.org/resource/ISSN/xxxx-xxxx
        $sx = '';
        $url = $link = '<a href="https://portal.issn.org/resource/ISSN/$issn" target="new_.$issn." class="btn-outline-primary rounded-3 p-2">' . bsicone('url', 24) . ' $issn</a>';
        if ($dt['jnl_issn'] != '') {
            $issn = $dt['jnl_issn'];
            $link = troca($url, '$issn', $issn);
            $sx .= 'ISSN: ' . $link;
        }
        if ($dt['jnl_eissn'] != '') {
            $sx .= ' - ';
            $issn = $dt['jnl_eissn'];
            $link = troca($url, '$issn', $issn);
            $sx .= 'eISSN: ' . $link;
        }
        return $sx;
    }

    function start_end($dt)
        {
            $dd = '';
            $di = $dt['jnl_ano_inicio'];
            $df = $dt['jnl_ano_final'];

            if ($di != '')
                {
                    $dd .= $di.'-';
                } else {
                    if ($df != '')
                        {
                            $dd .= $df;
                        }
                }
            return $dd;
        }

    function openaccess($dt=array())
        {
            $dt = array();
            $sx = 'OL';
            return $sx;
        }

    function journal_header($dt, $resume = true)
    {
        $Issues = new \App\Models\Base\Issues();
        $sx = '';
        if (!is_array($dt)) {
            $sx = bsmessage('Erro de identificação do ISSUE/Jornal', 3);
            return $sx;
            exit;
        }

        $idj = $dt['jnl_frbr'];
        $Cover = new \App\Models\Base\Cover();
        $img = '<img src="' . $Cover->image($dt['id_jnl']) . '" class="img-fluid">';
        $img = '';
        $sx = '';
        $url = PATH . COLLECTION . '/v/' . $idj;
        $jnl = h(anchor($url, $dt['jnl_name']), 3);

        $jnl .= '<div class="row">';
        //pre($dt);
        $jnl .= bsc($this->start_end($dt), 4);
        $jnl .= bsc($this->issn($dt), 8);
        //$jnl .= bsc($this->url($dt), 4);
        //$jnl .= bsc($this->active($dt), 8);
        $jnl .= '</div>';

        if (1==1) {
            $Oaipmh = new \App\Models\Oaipmh\Index();
            $jnl .= $Oaipmh->links($dt['id_jnl']);
        }


        /********** Actions */
        $sa = '';
        $sa .= $Issues->PainelAdmin($dt['id_jnl']);

        $sx .= bsc($jnl, 10);
        $sx .= bsc($img, 1, 'p-2');
        $sx .= bsc($sa, 1, 'p-2 text-end');
        $sx = bs($sx);

        return $sx;
    }

    function viewid($id)
    {
        $sx = '';
        $dt = $this->find($id);

        /************** ISSUES */
        $Issues = new \App\Models\Base\Issues();
        $Harvesting = new \App\Models\Base\Harvesting();
        $OAI = new \App\Models\Oaipmh\Index();

        $painel = $OAI->painel($dt);

        $sh = $this->journal_header($dt);

        $subp = $Harvesting->painel($dt);

        $jn_rdf = $dt['jnl_frbr'];
        if ($jn_rdf == 0)
            {
               $sx .= bsc(bsmessage("ERRO: JN_RDF not defined"),9);
               $sx .= bsc($painel, 3);
            } else {
                $sx .= bsc($subp, 9);
                $sx .= bsc($painel, 3);
            }


        /********* ISSUE */
        $sx .= h("ISSUE");

        $sx .= bsc($Issues->issuesRow($id),12);
        $sx = bs($sx);



        /************************************************* Mostra edições */
        //$sx .= $JournalIssue->view_issue($jn_rdf);

        /********************************************** Botoes de edições */
        //$sx .= bs(bsc($JournalIssue->btn_new_issue($dt), 12, 'mt-4'));

        return $sh.$sx;
    }

    /********************************************************************************** ADMIN EDIT */
    function editar($id)
    {
        $this->id = $id;
        $this->path = PATH .  'admin/source';
        $this->path_back = PATH .  '/admin/source';
        if ($id > 0) {
            $dt = $this->find($id);
            $sx = h($dt['jnl_name'], 1);
        } else {
            $sx = h(lang('Editar'), 1);
        }

        $sx .= form($this);
        $sx = bs(bsc($sx, 12));
        return $sx;
    }



    /******************************************** MOSTRA LISTA DE PUBLICAÇÕES */
    function xtableview($ax)
    {
        echo "OK2";
        $ax = (array)$ax;
        $fld = $ax['allowedFields'];
        $this->where("jnl_collection = 'JA'");
        $this->ORwhere("jnl_collection = 'JE'");
        $this->ORwhere("jnl_collection = 'EV'");
        if (isset($_POST['search_field']))
            {
                $fldn = $fld[$_POST['search_field']];
                $this->like($fldn,get("search"));
            }
        $this->path = (PATH . 'admin/source');
        $sx = tableview($this);
        $sx = bs(bsc($sx, 12));
        return $sx;
    }
}