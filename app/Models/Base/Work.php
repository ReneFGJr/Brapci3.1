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

    function show($id)
    {
        $RDF = new \App\Models\Rdf\RDF();
        $dt = $RDF->le($id);
        $dd = $dt['data'];
        for ($r = 0; $r < count($dd); $r++) {
            $line = $dd[$r];
            $class = $line['c_class'];
            //echo '==>'.$class;
        }
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

        if (count($sel) == 0)
            {
                $sx .= lang('brapci.nothing_selected');
                $sx .= ' | ';
                $sx .= $markall;
            } else {
                $sx .= lang('brapci.with').' '.count($sel).' '.lang('brapci.work_selected');
                $sx .= ' | ';
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
