<?php

namespace App\Models\Base;

use CodeIgniter\Model;

class Work extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'work';
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

    function showHTML($dt)
    {
        echo "ok - showHTML - RDF WORK";
        exit;
        $sx = view('RDF/work', $dt);
        return $sx;
    }

    function show($dt)
    {
        $Socials = new \App\Models\Socials();
        $RDF = new \App\Models\Rdf\RDF();
        $MidiasSociais = new \App\Models\MidiasSociais\Index();
        $Metadados = new \App\Models\Base\Metadata();
        $Download = new \App\Models\Base\Download();
        $Altmetrics = new \App\Models\MetricStudy\Altmetrics();
        $sc = '';

        $sx = '';
        if (!is_array($dt)) {
            $dt = round($dt);
            $dt = $RDF->le($dt);
        }
        $idc = $dt['concept']['id_cc'];

        /***************************************** Recupe dados */
        $da = array();
        $RDF = new \App\Models\Rdf\RDF();

        $class = $dt['concept']['c_class'];
        //echo '==>'.$class;
        switch($class)
            {
                case 'BookChapter':
                    $bookID = $RDF->extract($dt, 'hasBookChapter');
                    /* DAdos do Livro */
                    $dc = $Metadados->metadata($dt);

                    $da = $RDF->le($bookID[0]);
                    $da = $Metadados->metadata($da);
                    $da['Chapter'] = $dc;
                break;

                default:
                    $da = $Metadados->metadata($dt);
                break;
            }
        $da['class'] = $class;

        /************************************************** Midias Sociais */
        $da['id_cc'] = $idc;
        $da['MidiasSociais'] = $MidiasSociais->sharing($da);

        /************************************************************* BUGS */
        $Bugs = new \App\Models\Functions\Bugs();
        $da['bugs'] = $Bugs->show($idc);

        /************************************************************* BUGS */
        $Cited = new \App\Models\AI\Cited\Index();
        $da['nlp'] = $Cited->show($idc);

        /************************************************************ VIEWS */
        $ViewsRDF = new \App\Models\Functions\ViewsRDF();
        $da['views'] = $ViewsRDF->show($idc);

        /************************************************************ VIEWS */
        $Cited = new \App\Models\Cited\Index();
        $da['cited'] = $Cited->citation_total($idc);

        /****************************************************** REFERECNIAS */
        $da['reference'] = $this->show_reference($idc);
        $Citation = new \App\Models\Cited\Index();


        /********************************************************* CITACOES */
        $da['Citation'] = $Citation->show_ref($idc);

        /************************************************************ ISSUE */
        $issue = $RDF->extract($dt, 'hasIssue');
        if (isset($issue[0]))
            {
                $da['issue_id'] = $issue[0];
            }

        pre($da);
        /********************************************************* ALTMETRICS */
        if (isset($da['DOI']))
        {
            $DOI = $da['DOI'];
            $da['altmetrics'] = $Altmetrics->plum($DOI);
            $da['altmetrics'] .= $Altmetrics->altmetrics($DOI);
        } else {
            $da['altmetrics'] = '';
        }

        /********************************************************* DOWNLOADS */
        $da['files'] = $Download->show_resources($da);
        if (isset($da['summary']) and ($da['summary'] != ''))
            {
                $da['summary'] = '<center>'.
                    h(lang('brapci.summary'),4).
                    '</center>'.$da['summary'];
            }

        /************************************************** Botoes de Edição */
        echo "OK";
        exit;
        $da['edit'] = $Socials->getAccess("#ADM#CAT#ENA");

        if ($da['edit'] == 1) {
            $img_ia = '<img src="'.URL.'/img/icons/logo_brapci_ia.svg" height="28" title="IA Brapci Process">';


            /*
            $da['edit'] = '<a href="' . PATH . COLLECTION . '/a/' .
                    $dt['concept']['id_cc'] . '">' . bsicone('edit', 32) . '</a>';

            $da['edit'] .= '<a href="#" onclick="if (confirm(\'Confirma exclusão\'))
                    { newwin(\'' . PATH . '/rdf/concept/exclude/' . $dt['concept']['id_cc'] . '\',600,300); }"
                    style="color: red;" class="ms-2">' . bsicone('del', 32) . '</a>';
            $da['edit'] .= '<a href="' . PATH . '/ai/nlp/fulltext/' . $dt['concept']['id_cc'] . '">' .
                    $img_ia . '</a>';
            */
            $sc .= $RDF->view_data($dt);
            echo "OK$sc"; exit;
        }

        echo "OK3";
        exit;


        /******************** MOSTRAR */
        switch ($class) {
            case 'Article':
                $sx .= view('Brapci/Base/WorkArticle', $da);
                break;
            case 'Book':
                $sx .= view('Brapci/Base/WorkBook', $da);
                break;
            case 'BookChapter':
                $sx .= view('Brapci/Base/WorkBookChapter', $da);
                break;
            case 'Proceeding':
                $Issue = new \App\Models\Base\Issues();
                $idi = $Issue->where('is_source_issue', $da['issue_id'])->first();
                $da['sub_header'] = $Issue->issue($idi['id_is']);
                $da['issue'] = '';
                $sx .= view('Brapci/Base/Work', $da);
                //$sx .= $RDF->view_data($dt);
                break;
            case '/benancib':
                $Issue = new \App\Models\Base\Issues();

                if (isset($da['issue_id']))
                    {
                        $idi = $Issue->where('is_source_issue', $da['issue_id'])->first();
                        $da['sub_header'] = $Issue->issue($idi['id_is']);
                        $da['issue'] = '';
                    } else {
                        $idi = array();
                        $da['sub_header'] = '';
                        $da['issue'] = '';
                    }

                $sc = '';


                $sx .= view('Brapci/Base/Work', $da);
                break;
            default:

                break;
        }
        if ($Socials->getAccess("#ADM#CAT#ENA"))
            {
                $sx .= bs(bsc('<a href="#data" onclick="showw();">'.bsicone('upload',10).'</a>',12,));
                $sa = '<div name="data" id="data" style="display: none;">';
                $sa .= $RDF->view_data($dt);
                $sa .= '</div>';
                $sx .= bs(bsc($sa,12));
                $sx .= '
                <script>
                function showw()
                    {
                        $("#data").toggle("slow");
                    }
                </script>';

                $sx .= h('Elastic');
                $Elastic = new \App\Models\ElasticSearch\Register();
                //$sx .= $Elastic->show($idc);
            }

        return $sx;
    }

    function getWorkMark()
    {
        if (isset($_SESSION['sel'])) {
            $sel = $_SESSION['sel'];
            $sel = (array)json_decode($sel);
        } else {
            $sel = array();
        }
        return $sel;
    }

    function putWorkMark($sel)
    {
        if (count($sel) == 0)
            {
                unset($_SESSION['sel']);
            } else {
                $_SESSION['sel'] = json_encode($sel);
            }
        return true;
    }

    function workClear()
        {
            $sel = array();
            $this->putWorkMark($sel);
        }

    function workMark($id,$ck)
        {
        /************************************** CHECK */
        $sel = $this->getWorkMark();
        if (($id !=  '0') and ($id != '')) {
            if ($ck == 'true') {
                $sel[$id] = 1;
            } else {
                if ($ck == 'true') {
                    $sel[$id] = 1;
                } else {
                    unset($sel[$id]);
                }
            }
        } else {
            echo '<script>alert("OPS ID inválido: ' . $id . '");</script>';
        }

        $this->putWorkMark($sel);
        }

    function WorkSelected()
    {
        $uri = $_SERVER['REQUEST_URI'];
        $dt = $_GET;
        $data = '';
        if (count($dt) > 0)
            {
                $data = json_encode($dt);
                $data = troca($data,'"','¢');
            }

        $sx = lang('brapci.library_cart').' ';
        $sel = $this->getWorkMark();
        $markall = '<a href="#" onclick="markAll();">' . lang('brapci.work_select_all') . '</a>';
        $markall .= '<input type="hidden" id="uri" name="uri" value="' . $uri . '">';
        $markall .= '<input type="hidden" id="query" name="query" value="' . $data . '">';

        $Socials = new \App\Models\Socials();
        $saveAll = '';
        if ($Socials->getAccess("#ADM#USR"))
            {
                $saveAll = '<a href="' . PATH . '/mark/analyse">' . lang('brapci.MarkAnalyse') . '</a>';
                $saveAll .= ' | ';
                $saveAll .= '<a href="' . PATH . '/mark/saveMark">' . lang('brapci.MarkSave') . '</a>';
                $saveAll .= ' | ';
            } else {
                $saveAll = 'not loged | ';
            }


        if (count($sel) == 0)
            {
                $sx .= lang('brapci.nothing_selected');
                $sx .= ' | ';
                $sx .= $markall;
            } else {
                $sx .= lang('brapci.with').' '.count($sel).' '.lang('brapci.work_selected');
                $sx .= ' | ';
                $sx .= $saveAll;
                $sx .= '<a href="#" onclick="markClear();">'.lang('brapci.work_selected_clear').'</a>';
                $sx .= ' | ';
                $sx .= $markall;
            }
        return $sx;
    }

    function show_reference($id)
    {
        $sx = '';
        $RDF = new \App\Models\Rdf\RDF();
        $MARK = new \App\Models\Base\Mark();
        $chk = '';
        if ((isset($_SESSION['sel'])) and ($_SESSION['sel'] != '')) {
            $sel = (array)json_decode($_SESSION['sel']);
            $wid = 'w' . $id;
            if ((isset($sel[$wid])) and ($sel[$wid] == '1')) {
                $chk = 'checked';
            }
        }
        $sx .= '<span class="reference">';
        $sx .= $MARK->mark($id);
        $sx .= $RDF->c($id,'abnt') . cr();
        $sx .= '</span>';
        return $sx;
    }
}
