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
        $sx = '';
        if (!is_array($dt)) {
            $dt = round($dt);
            $dt = $RDF->le($dt);
        }
        $da = array();
        $RDF = new \App\Models\Rdf\RDF();

        $dd = $dt['data'];

        $dc = $dt['concept'];
        $idc = $dc['id_cc'];
        $class = $dc['c_class'];

        $da['authors'] = '';
        $da['keywords'] = array();
        $da['issue'] = '-na-';
        $da['PDF'] = array();
        $da['URL'] = array();
        $da['id_cc'] = $dt['concept']['id_cc'];
        $da['Section'] = array();
        $da['isbn'] = '';
        $da['editora'] = '';
        $da['subject'] = array();
        $da['cover'] = '';
        $da['year'] = '';
        $da['idioma'] = '';
        $da['pages'] = '';
        $da['editora_local'] = '';

        for ($r = 0; $r < count($dd); $r++) {
            $line = $dd[$r];
            $lang = $line['n_lang'];
            $lang2 = $line['n_lang2'];
            $class = trim($line['c_class']);
            switch ($class) {
                case 'isPlaceOfPublication':
                    $da['editora_local'] .= $RDF->c($line['d_r1']) . '$';
                    break;
                case 'hasPage':
                    $da['pages'] .= $RDF->c($line['d_r1']). ' ';
                    break;
                case 'hasLanguageExpression':
                    $da['idioma'] .= $RDF->c($line['d_r1']);
                    break;
                case 'hasClassificationAncib':
                    $da['classification']['Ancib'] = $RDF->c($line['d_r1']);
                    break;
                case 'hasClassificationCDD':
                    $da['classification']['CDD'] = $RDF->c($line['d_r1']);
                    break;
                case 'dateOfPublication':
                    $da['year'] = $RDF->c($line['d_r1']);
                    break;
                case 'hasISBN':
                    $da['isbn'] .= $RDF->c($line['d_r1']);
                    break;
                case 'isPublisher':
                    $da['editora'] .= $RDF->c($line['d_r1']);
                    break;
                case 'hasIssueProceedingOf':
                    $da['issue'] = $RDF->c($line['d_r1']);
                    break;
                case 'hasIssueOf':
                    $da['issue'] = $RDF->c($line['d_r1']);
                    break;
                case 'hasSectionOf':
                    if (!isset($da['Section'])) {
                        $da['Section']  = array();
                    }
                    array_push($da['Section'], $line['n_name2']);
                    break;
                case 'hasFileStorage':
                    $PDF['file'] = $line['n_name2'];
                    $PDF['id'] = $line['d_r1'];
                    array_push($da['PDF'], $PDF);
                    break;
                case 'hasCover':
                    $da['cover'] = $RDF->c($line['d_r2']);
                    break;
                case 'hasTitle':
                    $da['Title'][$lang] = '<p class="abstract">' . $line['n_name'] . '</p>';
                    break;
                case 'hasAbstract':
                    $da['Abstract'][$lang] = '<p class="abstract">' . $line['n_name'] . '</p>';
                    break;
                /************* Authors */
                case 'hasAuthor':
                    $name = '<a href="' . URL . COLLECTION . '/v/' . $line['d_r2'] . '">' . $line['n_name2'] . '</a>';
                    $da['authors'] .= $name.'$';
                    break;
                case 'hasOrganizator':
                    $name = '<a href="' . URL . COLLECTION . '/v/' . $line['d_r2'] . '">' . $line['n_name2'] . '</a>';
                    $da['authors'] .= $name . '$';
                    break;
                case 'hasSubject':
                    $name = '<a href="' . URL . COLLECTION . '/v/' . $line['d_r2'] . '">' . $line['n_name2'] . '</a>';
                    if (!isset($da['keywords'][$lang2])) {
                        $da['keywords'][$lang2] = array();
                    }
                    array_push($da['keywords'][$lang2], $name);
                    break;
                case 'prefLabel':
                    break;
                case 'isPubishIn':
                    break;
                case 'hasSource':
                    break;
                case 'hasId':
                    break;
                case 'dateOfAvailability':
                    break;
                case 'hasRegisterId':
                    array_push($da['URL'], trim($line['n_name']));
                    break;
                default:
                    jslog('Class not found: ' . $class);
                    //$sx .= bsmessage('Class not found - ' . $class, 3);
                    break;
            }
        }

        switch (COLLECTION) {
            case '/proceedings':
                $da['MidiasSociais'] = $MidiasSociais->sharing($da);
                $sx .= view('Proceeding/Base/Work', $da);
                //$sx .= $RDF->view_data($dt);
                break;
            case '/benancib':
                $sx .= view('Benancib/Base/Work', $da);
                //$sx .= $RDF->view_data($dt);
                break;
            case '/books':
                $sx .= view('Books/Base/Work', $da);
                break;
            default:
                $sx .= view('Benancib/Base/Work', $da);
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
        if ($Socials->getAccess("#ADM"))
            {
                $saveAll = '<a href="' . PATH . COLLECTION . '/analyse">' . lang('brapci.MarkAnalyse') . '</a>';
                $saveAll .= ' | ';
                $saveAll .= '<a href="' . PATH . COLLECTION . '/saveMark">' . lang('brapci.MarkSave') . '</a>';
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
