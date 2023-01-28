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
        echo "ok";
        exit;
        $sx = view('RDF/work', $dt);
        return $sx;
    }


    function show($dt)
    {
        $RDF = new \App\Models\Rdf\RDF();

        $MidiasSociais = new \App\Models\MidiasSociais\Index();
        $Metadados = new \App\Models\Base\Metadata();
        $Download = new \App\Models\Base\Download();
        $sx = '';
        if (!is_array($dt)) {
            $dt = round($dt);
            $dt = $RDF->le($dt);
        }
        $da = array();
        $RDF = new \App\Models\Rdf\RDF();

        $da = $Metadados->metadata($dt);

        /*
        $dd = $dt['data'];

        $dc = $dt['concept'];
        $idc = $dc['id_cc'];
        $class = $dc['c_class'];

        $da['Title'] = array();
        $da['authors'] = '';
        $da['keywords'] = array();
        $da['issue'] = '-na-';
        $da['PDF'] = array();
        $da['URL'] = array();
        $da['id_cc'] = $dt['concept']['id_cc'];
        $da['Section'] = array();
        $da['isbn'] = '';
        $da['editora'] = '';
        $da['subject'] = '';
        $da['cover'] = '';
        $da['year'] = '';
        $da['idioma'] = '';
        $da['pages'] = '';
        $da['editora_local'] = '';
        $da['links'] = '';
        $da['class'] = ('brapci.'.$dt['concept']['c_class']);
        $da['issue_id'] = 0;
        $da['summary'] = '';
        $da['book'] = '';
        $da['reference'] = $RDF->c($idc);
        */

        $idc = $dt['concept']['id_cc'];
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

        $da['reference'] = $this->show_reference($idc);

        $da['files'] = $Download->show_resources($da);
        if (isset($da['summary']) and ($da['summary'] != ''))
            {
                $da['summary'] = '<center>'.
                    h(lang('brapci.summary'),4).
                    '</center>'.$da['summary'];
            }

    /*
        if (!isset($da['issue_id']))
            {
                echo '<br><br><br><br><br>';
                echo "ERRO EXPORT - WORK - ". COLLECTION.' class:'. $da['class'];
                //pre($dt);
            }
*/
//pre($da,false);
        switch (COLLECTION) {
            case '/proceedings':
                $Issue = new \App\Models\Base\Issues();
                $idi = $Issue->where('is_source_issue', $da['issue_id'])->first();
                $da['sub_header'] = $Issue->issue($idi['id_is']);
                $da['issue'] = '';
                $sx .= view('Brapci/Base/Work', $da);
                //$sx .= $RDF->view_data($dt);
                break;
            case '/benancib':
                $Issue = new \App\Models\Base\Issues();
                $idi = $Issue->where('is_source_issue',$da['issue_id'])->first();
                $da['sub_header'] = $Issue->issue($idi['id_is']);
                $da['issue'] = '';
                $Socials = new \App\Models\Socials();
                $sc = '';
                if ($Socials->getAccess("#ADM"))
                    {
                        $da['edit'] = '<a href="'.PATH.COLLECTION.'/a/'.$dt['concept']['id_cc'].'">'.bsicone('edit').'</a>';
                        $sc .= $RDF->view_data($dt);
                    }

                $sx .= view('Brapci/Base/Work', $da);
                break;

            case '/books':
                $class = trim($da['class']);
                switch ($class)
                    {
                        case 'BookChapter':
                            $sx .= view('Books/Base/WorkChapterBook', $da);
                            break;

                        case 'Book':
                            $sx .= view('Books/Base/Work', $da);
                            break;

                        default:
                            echo "OPS BOOK CLASS NOT FOUND [".$da['class'].']';
                            break;
                    }
                break;
            default:
                switch($da['class'])
                    {
                        case 'brapci.BookChapter':
                            $da['Section'] = array(lang('brapci.BookChapter'));
                            $sx .= view('Books/Base/WorkChapterBook', $da);
                            $sx .= $RDF->view_data($dt);
                            break;

                        case 'brapci.Book':
                            $da['Section'] = array(lang('brapci.Book'));
                            $sx .= view('Books/Base/Work', $da);
                            $sx .= $RDF->view_data($dt);
                            break;

                        default:
                            $sx .= view('Brapci/Base/Work', $da);
                        break;
                    }
                break;
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
        $chk = '';
        if ((isset($_SESSION['sel'])) and ($_SESSION['sel'] != '')) {
            $sel = (array)json_decode($_SESSION['sel']);
            $wid = 'w' . $id;
            if ((isset($sel[$wid])) and ($sel[$wid] == '1')) {
                $chk = 'checked';
            }
        }
        $sx .= '<input type="checkbox" name="w' . $id . '" id="w' . $id . '" ' . $chk . ' onclick="markArticle(\'w' . $id . '\',this);"> ';
        $sx .= $RDF->c($id) . cr();
        return $sx;
    }
}
