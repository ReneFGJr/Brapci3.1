<?php

namespace App\Models\RDF2;

use CodeIgniter\Model;

class RDF extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'rdfs';
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

    function index($d1, $d2, $d3, $d4, $cab)
    {
        $sx = '';
        $RSP = [];

        switch ($d1) {
            case 'a':
                $RDFform = new \App\Models\RDF2\RDFform();
                $sx .= $cab;
                $sx .= $RDFform->editRDF($d2);
                return bs(bsc($sx,12));
                break;
            case 'resume':
                $RDFdata = new \App\Models\RDF2\RDFdata();
                $sx .= $RDFdata->resume();
                return $sx;
                break;
            case 'withoutClass':
                $RDFdata = new \App\Models\RDF2\RDFdata();
                $sx .= $RDFdata->withoutClass($d2);
                return $sx;
                break;
            case 'rules':
                $RDFclassDomain = new \App\Models\RDF2\RDFclassDomain();
                $sx .= $RDFclassDomain->rules($d2);
                return $sx;
                break;
            case 'v':
                $sx = $this->view($d2);
                break;
            case 'popup':
                $data['page_title'] = 'Brapci - POPUP';
                $data['bg'] = 'bg-pq';
                $sx .= $this->popup($d2, $d3, $d4);
                return $sx;
                break;
            case 'form':
                $data['page_title'] = 'Brapci - POPUP';
                $data['bg'] = 'bg-pq';
                $sx = '';
                $sx .= view('Brapci/Headers/header', $data);
                $RDFform = new \App\Models\RDF2\RDFform();
                $sx .= $RDFform->index($d2, $d3, $d4);
                return $sx;
                break;
            case 'import':
                $RSP = $this->import();
                break;
            case 'source':
                break;
            case '404':
                $RSP = $this->default();
                break;
            case 'Class':
                $sx = '';
                $RDFclass = new \App\Models\RDF2\RDFclass();
                $RDFconcept = new \App\Models\RDF2\RDFconcept();
                $RDFtoolsImport = new \App\Models\RDF2\RDFtoolsImport();

                if ($d2 == '') {
                    $Class = $RDFclass->getClasses();
                    $sx = '<div style="column-count: 3;">';
                    $sx .= '<ul>';
                    foreach ($Class as $id => $line) {
                        $link = '<a href="' . PATH . '/rdf/Class/' . $line['Class'] . '">';
                        $linka = '</a>';
                        $sx .= '<li>' . $link . $line['Class'] . $linka . '</li>' . cr();
                    }
                    $sx .= '</ul>';
                    $sx .= '</div>';
                } else {
                    $dt =  $RDFclass->get($d2);
                    $sx .= h("Class", 6);
                    $sx .= h($dt['Class']);
                    $sx .= h($dt['prefix'], 5);

                    /****** Total registros */
                    $dtt = $RDFconcept->select('count(*) as total')
                        ->where('cc_class', $dt['id'])
                        ->groupBy('cc_class')
                        ->first();
                    if ($dtt != []) {
                        $sx .= h('Total of ' . number_format($dtt['total'], 0, ',', '.') . ' registers', 4);
                    }


                    $sx .= '<hr>';
                    $sx .= anchor(PATH . '/rdf/Class/', 'Voltar', ['class' => 'btn btn-outline-primary']);
                    $sx .= anchor(PATH . '/rdf/Class/' . $d2 . '/reimport', 'Reimporta', ['class' => 'ms-2 btn btn-outline-warning']);
                    $sx .= anchor(PATH . '/api/rdf/in/all', 'Importa', ['class' => 'ms-2 btn btn-outline-danger']);
                    if ($d3 == 'reimport') {
                        $RDFtoolsImport->reimport($dt['id']);
                    }
                }
                return bs(bsc($sx));
                break;
            default:
                return bs(bsc($this->menu(), 12));
                break;
        }
        $RSP['time'] = date("Y-m-dTH:i:s");
        echo json_encode($RSP);
        exit;
    }

    function menu()
    {
        $menu = [];
        $menu[PATH . '/rdf/Class'] = "Classes";
        $menu[PATH . '/rdf/withoutClass/-1'] = "WithOutClasses (-1)";
        $menu[PATH . '/rdf/withoutClass/0'] = "WithOutClasses (0)";
        $menu[PATH . '/rdf/withoutClass/1'] = "WithOutClasses (1)";
        $menu[PATH . '/rdf/resume'] = "Resume";
        $menu[PATH . '/rdf/rules'] = "Ontology (Rules)";
        return menu($menu);
    }

    function view($id)
        {
            $dt = $this->v($id);
            pre($dt);
        }

    function index_list($i,$l='A')
        {
            $cp = 'n_name, n_lang, id_cc';
            $RDFclass = new \App\Models\RDF2\RDFclass();
            $RDFconcept = new \App\Models\RDF2\RDFconcept();
            $idc = $RDFclass->getClass($i);
            $dt = $RDFconcept
                ->select($cp)
                ->join('brapci_rdf.rdf_literal', 'id_n = cc_pref_term')
                ->where('cc_class',$idc)
                ->like('n_name',$l,'after')
                ->orderBy('n_name')
                ->findAll(10000);
            return $dt;

        }

    function popup($d1, $d2, $d3)
    {
        $sx = '';
        $RDFdata = new \App\Models\RDF2\RDFdata();

        switch ($d1) {
            case 'add':
                $sx .= '<div class="text-center">';
                $RDFform = new \App\Models\RDF2\RDFform();
                $sx .= $RDFform->add($d2,$d3);
                $sx .= '</div>';
                break;
            case 'delete':
                $sx .= '<div class="text-center">';
                ############################## DELETE
                $conf = get("confirm");
                if ($conf != '') {

                    $sx .= '<h1 class="text-center">' . lang('brapci.excluded_item') . '</h1>';
                    $sx .= '<span class="btn btn-outline-primary" onclick="wclose();">' . lang("brapci.close") . '</span>';
                    $RDFdata->where('id_d', $d2)->delete();
                } else {
                    $dt = $RDFdata
                        ->find($d2);
                    $sx .= '<a class="btn btn-outline-danger" href="' . PATH . '/popup/rdf/delete/' . $d2 . '?confirm=True">' . lang("brapci.exclude") . '</a>';
                }
                $sx .= '</div>';
        }
        return $sx;
    }

    /************* Default */
    function default()
    {
        $dt = [];
        $dt['status'] = '404';
        $dt['message'] = 'Action not informed';
        return $dt;
    }

    function show_class($dt)
    {
        $sx = '';
        $cnt = $dt['concept'];
        $sa = '<span style="font-size: 1.6em; font-weight: bold">' . $cnt['n_name'] . '</span>';
        $sa .= '<br>';
        $sa .= $cnt['prefix_ref'] . '.' . $cnt['c_class'];

        $sb = $cnt['n_lang'];
        $sb .= '<br><span class="small">Update: ' . $cnt['cc_update'] . '</span>';

        $sx .= bsc($sa, 10, 'border-bottom border-secondary');
        $sx .= bsc($sb, 2, 'border-bottom border-secondary text-end');
        return bs($sx);
    }

    /********************* getClassType */
    function getClassType($id)
    {
        $RDFconcept = new \App\Models\RDF2\RDFconcept();
        $dt = $RDFconcept
            ->join('rdf_class', 'cc_class = id_c')
            ->where('id_cc', $id)->first();
        if ($dt != null) {
            return $dt['c_class'];
        }
        return "";
    }

    /************* V */
    function v($id)
    {
        $RDFconcept = new \App\Models\RDF2\RDFconcept();
        $RDFdata = new \App\Models\RDF2\RDFdata();

        $data = [];
        $data['concept'] = $RDFconcept->le($id);

        $data['data'] = $RDFdata->le($id);

        return $data;
    }

    /************* V */
    function resume()
    {
        $RDFconcept = new \App\Models\RDF2\RDFconcept();
        $dt = $RDFconcept
            ->select('count(*) as total, c_class')
            ->join('rdf_class', 'id_c = cc_class')
            ->groupBy('c_class')
            ->orderBy('c_class')
            ->findAll();
        $d = [];
        foreach ($dt as $id => $line) {
            $class = $line['c_class'];
            $d[$class] = $line['total'];
        }
        pre($d);
    }

    function le($id)
    {
        $RDFconcept = new \App\Models\RDF2\RDFconcept();
        $RDFdata = new \App\Models\RDF2\RDFdata();
        $d = [];
        $d['concept'] = $RDFconcept->le($id);
        $d['data'] = $RDFdata->le($id);

        /************************* Remover */
        if ($d['data'] == []) {
            $RDFtoolsImport = new \App\Models\RDF2\RDFtoolsImport();
            $RDFtoolsImport->importRDF($id);
            $d['data'] = $RDFdata->le($id);
        }
        return $d;
    }

    function valid($id)
    {
        $RDFconcept = new \App\Models\RDF2\RDFconcept();
        $dt = $RDFconcept->find($id);
        if ($dt == null) {
            return false;
        } else {
            return true;
        }
    }

    /************* Import */
    function import()
    {
        $sx = '';
        $sx .= form_open_multipart();
        $sx .= form_upload('OWL');
        $sx .= form_submit('action', 'Send file');
        $sx .= form_close();

        if (isset($_FILES['OWL'])) {
            $RDFtoolsImport = new \App\Models\RDF2\RDFtoolsImport();
            $file = $_FILES['OWL']['tmp_name'];

            $sx .= $RDFtoolsImport->import($file);
        }
        echo $sx;
        exit;
    }

    function recoverClass($class, $limit = 20, $offset = 0, $ord = 'N')
    {
        $ord = substr($ord, 0, 1);
        $RDFclass = new \App\Models\RDF2\RDFclass();
        $RDFconcept = new \App\Models\RDF2\RDFconcept();

        if ((sonumero($class)) != $class) {
            $class = $RDFclass->getClass($class);
        }
        if ($limit == '') {
            $limit = 20;
        }
        $dt = $RDFconcept
            ->select('id_cc')
            ->where('cc_class', $class);
        if ($ord = 'd') {
            $RDFconcept->orderBy('id_cc desc');
        }
        $dt = $RDFconcept->findAll($limit, $offset);
        return $dt;
    }

    function view_data($dt)
    {
        $RDFdata = new \App\Models\Rdf2\RDFdata();
        return $RDFdata->view_data($dt);
    }
    function extract($dt, $prop, $type = 'F')
    {
        /*
            F->first
            A->Array
            S->string (todos)
            */
        $dt = $dt['data'];
        $dr = [];
        $st = '';

        foreach ($dt as $id => $line) {
            if ($line['Property'] == $prop) {
                /******************************** FIRST */
                if ($type == 'F') {
                    return ($line['Caption']);
                }
                array_push($dr, $line['ID']);
                $st .= $line['Caption'] . ';';
            }
        }
        if ($type == 'A') {
            return $dr;
        }
        if ($type == 'S') {
            return $st;
        }
    }
    function E404()
    {
        $sx = '<h1>' . 'ERROR: 404' . '</h1>';
        $sx .= '<p>' . lang('rdf.concept_was_deleted') . '</p>';
        $sx .= '<button onclick="history.back()">Go Back</button>';
        return ($sx);
    }
}
