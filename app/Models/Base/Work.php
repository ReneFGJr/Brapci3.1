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

        $class = $dt['concept']['c_class'];

        switch($class)
            {
                case 'BookChapter':
                    $book = $RDF->extract($dt, 'hasBookChapter');
                    $db = $RDF->le($book[0]);
                    $da = $Metadados->metadata($db);
                    $da['class'] = 'brapci.'.$class;

                    $db = $Metadados->metadata($dt);

                    if (isset($db['title']))
                        {
                            $da['titleChapet'] = $db['title'];
                        }
                    if (isset($db['idioma'])) {
                        $da['idiomaChapet'] = $db['idioma'];
                    }
                    if (isset($db['authors'])) {
                        $da['authorsChapet'] = $db['authors'];
                    }
                    if (isset($db['DOI'])) {
                        $da['DOIChapet'] = $db['DOI'];
                    }

                    if (isset($db['abstract'])) {
                        $da['abstractChapt'] = $db['abstract'];
                    }
                break;

                default:
                    $da = $Metadados->metadata($dt);
                break;
            }
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
        $Citation = new \App\Models\Cited\Index();
        $da['Citation'] = $Citation->show_ref($idc);

        $da['files'] = $Download->show_resources($da);
        if (isset($da['summary']) and ($da['summary'] != ''))
            {
                $da['summary'] = '<center>'.
                    h(lang('brapci.summary'),4).
                    '</center>'.$da['summary'];
            }

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
                $Socials = new \App\Models\Socials();
                $sc = '';
                if ($Socials->getAccess("#ADM"))
                    {
                        $da['edit'] = '<a href="'.PATH.COLLECTION.'/a/'.$dt['concept']['id_cc'].'">'.bsicone('edit').'</a>';
                        $da['edit'] .= '<a href="#" onclick="if (confirm(\'Confirma exclusão\')) { newwin(\''. PATH . '/rdf/concept/exclude/' . $dt['concept']['id_cc'] . '\',600,300); }" style="color: red;">' . bsicone('del') . '</a>';
                        $sc .= $RDF->view_data($dt);
                    }

                $sx .= view('Brapci/Base/Work', $da);
                break;

            case '/books':
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
        if ($Socials->getAccess("#ADM#CAT#ENA"))
            {

                $sx .= $RDF->view_data($dt);

                $e = array(214290, 214294, 214297, 214301, 214303, 214304, 214308, 214309, 214313, 214317, 214321, 214324, 214328, 214330, 214333, 214337, 214341, 214343, 214346, 214350, 214352, 214355, 214357, 214360, 214361, 214366, 214367, 214371, 214374, 214378, 214381, 214387, 214389, 214391, 214392, 214394, 214396, 214399, 214402, 214405, 214407, 214410, 214411, 214414, 214417, 214424, 214428, 214429, 214432, 214435, 214438, 214443, 214447, 214451, 214452, 214456, 214459, 214463, 214466, 214472, 214475, 214478, 214480, 214484, 214485, 214487, 214491, 214493, 214497, 214500, 214502, 214505, 214507, 214512, 214517, 214518, 214520, 214525, 214526, 214528, 214532, 214535, 214537, 214539, 214542, 214545, 214547, 214549, 214553, 214554, 214557, 214558, 214562, 214565, 214568, 214572, 214576, 214579, 214582, 214585, 214588, 214589, 214593, 214596, 214601, 214604, 214608, 214609, 214611, 214613, 214616, 214619, 214622, 214624, 214626, 214628, 214631, 214633, 214637, 214640, 214643, 214648, 214649, 214650, 214652, 214654, 214656, 214665, 214667, 214669, 214673, 214676, 214678, 214680, 214683, 214686, 214687, 214691, 214692, 214695, 214698, 214699, 214702, 214704, 214707, 214709, 214712, 214715, 214717, 214721, 214726, 214728, 214732, 214736, 214743, 214747, 214748, 214750, 214752, 214754, 214757, 214761, 214764, 214769, 214773, 214777, 214779, 214785, 214790, 214794, 214797, 214799, 214804, 214806, 214809, 214812, 214815, 214818, 214822, 214824, 214827, 214829, 214831, 214834, 214837, 214840, 214844, 214845, 214849, 214851, 214854, 214856, 214861, 214864, 214866, 214868, 214873, 214877, 214880, 214883, 214886, 214889, 214893, 214896, 214898, 214903, 214906, 214908, 214910, 214915, 214917, 214920, 214924, 214927, 214931, 214935, 214936, 214939, 214942, 214946, 214948, 214951, 214953, 214957, 214959, 214961, 214962, 214965, 214968, 214970, 214972, 214975, 214976, 214978, 214981, 214985, 214986, 214992, 214995, 214997, 215000, 215003, 215004, 215007, 215011, 215013, 215017, 215020, 215021, 215023, 215027, 215030, 215034, 215040, 215044, 215046, 215053, 215056, 215057, 215058, 215063, 215065, 215067, 215069, 215075, 215076, 215080, 215084, 215087, 215091, 215093, 215096, 215099, 215104, 215108, 215110, 215113, 215114, 215118, 215120, 215123, 215125, 215128, 215130, 215134, 215137, 215138, 215139, 215142, 215143, 215144, 215147, 215151, 215158, 215160, 215163, 215165, 215168, 215169, 215170, 215174, 215179, 215180, 215182, 215187, 215191, 215195, 215198, 215202, 215203, 215205, 215208, 215210, 215214, 215215, 215218, 215219, 215221, 215222, 215227, 215230, 215234, 215237, 215240, 215243, 215245, 215247, 215250, 215252, 215255, 215261, 215264, 215267, 215268, 215270, 215272, 215274, 215276, 215281, 215285, 215286, 215288, 215291, 215294);
                foreach($e as $ide)
                    {
                        $RDF->exclude($ide);
                    }

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
