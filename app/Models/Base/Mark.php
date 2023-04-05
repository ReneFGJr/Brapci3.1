<?php

namespace App\Models\Base;

use CodeIgniter\Model;

class Mark extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'brapci_click.mark_save';
    protected $primaryKey       = 'id_mk';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_mk', 'mk_user', 'mk_selected',
        'mk_created_at', 'mk_update_at', 'mk_name'
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

    function index($cmd,$d1='',$d2='',$d3='')
        {
            $sx = '';
            switch($cmd)
                {
                    case 'analyse':
                        $AnalyseStudy = new \App\Models\MetricStudy\Analyse();
                        $sx .= $AnalyseStudy->index();
                        break;
                    default:
                        $sx .= $cmd . " $d1 $d2 $d3";
                        break;
                }
            return $sx;
        }

    function mark($id)
        {
            global $sel;
            $check = '';

            if (!isset($sel))
                {
                    $sel = [];
                    if (isset($_SESSION['sel']))
                        {
                            $sel = $_SESSION['sel'];
                            $sel = (array)json_decode($sel);
                        }
                }
            if (isset($sel[$id]))
                {
                    if ($sel[$id] == 1)
                        {
                            $check = 'checked';
                        }
                }
            $sx = '';
            $idf = 'mk_' . $id;
            $sx .= form_checkbox($idf,1,$check,['onchange'=> 'markArticle("'.$id.'",this);','class'=>'me-1']);
            return $sx;
        }

    function showId($id,$action='')
        {
            $sx = '';
            if ($action == 'load')
                {
                    $sx .= $this->restoreMark($id);
                    $sx .= bsmessage(lang('brapci.mark_restaured'));
                    return bs(bsc($sx,12));
                }
                $dt = $this
                ->select('id_mk, mk_user, mk_selected, mk_name, mk_created_at, mk_update_at, id_us, us_nome')
                ->join('brapci.users', 'mk_user = id_us')
//                ->join('rdf_class', 'cc_class = id_c')
                ->where('id_mk', $id)
                ->findAll();
            $dt = $dt[0];

            $data = $dt['mk_selected'];
            $data = (array)json_decode($data);

            $dt['data'] = $data;
            $sx = view('Mark/viewId',$dt);

            $sx .= bs(bsc($this->btn_mark_load($id)));
            return $sx;
        }
    function btn_mark_load($id)
        {
            $sx = '';
            $sx .= '<a href="'.PATH.'/mark/'.$id.'/load" class="btn btn-outline-primary">'.
                    lang('brapci.mark_load').'</a>';
            return $sx;
        }

    function btn_mark_analyse()
    {
        $sx = '';
        $sx .= '<a href="' . PATH . '/mark/analyse" class="btn btn-outline-primary">' .
        lang('brapci.mark_analyse') . '</a>';
        return $sx;
    }

    function listMark()
        {
            $sx = '';
            $user = $_SESSION['id'];
            $dt = $this->where('mk_user',$user)->orderby("mk_update_at")->findAll();
            $limit = 10;
            $lt = '<ul>';
            for($r=0;$r < count($dt);$r++)
                {
                    $line = $dt[$r];
                    $link = '<a href="'.PATH. '/mark/'.$line['id_mk'].'">';
                    $linka = '</a>';
                    $lt .= '<li>'.$link.$line['mk_name']. $linka.'</li>';
                }
            $lt .= '</ul>';

            $sx = '
			<div class="card h-100">
					<div class="card-header pb-0 p-3">
						<h6 class="mb-0">' . lang('social.researchs_register') . '</h6>
					</div>
					<div class="card-body p-3">
						<h6 class="text-uppercase text-body text-xs font-weight-bolder">' . lang('social.research') . '</h6>
						<ul class="list-group">';
            $sx .= '<li class="list-group-item border-0 px-0">' . lang('social.research_info') . '</li>';
            $sx .= $lt;
            $sx .= '</ul></div></div>';
            return $sx;
        }

    function showSession()
        {
            $RDF = new \App\Models\Rdf\RDF();
            $sx = '';
            if (isset($_SESSION['markName']))
                {
                    $nameMark = $_SESSION['markName'];
                } else {
                    $nameMark = lang('brapci.mark_new_session');
                }
            $sx .= h($nameMark,2);

            if (isset($_SESSION['sel']))
                {
                    $sel = (array)json_decode($_SESSION['sel']);
                } else {
                    $sel = array();
                }
                $tot = 0;
                $sx .= '<ol>';
                foreach($sel as $w=>$status)
                    {
                        $w = troca($w,'w','');
                        $sx .= '<li>'.$RDF->c($w). '</li>';
                    }
                $sx .= '</ol>';
            return $sx;
        }

    function restoreMark($id)
        {
        $user = $_SESSION['id'];
        $_SESSION['markName'] = '';

        $line = $this->find($id);
        $_SESSION['markId'] = $line['id_mk'];
        $_SESSION['markName'] = $line['mk_name'];
        $_SESSION['sel'] = $line['mk_selected'];

        $dt = $this->find($id);
        $sx = $this->showSession();
        return $sx;
        }

    function saveMark()
    {
        $Socials = new \App\Models\Socials();
        $sx = '';

        $user = $_SESSION['id'];
        if (isset($_SESSION['markName'])) {
            $idm = round($_SESSION['markName']);
            $dt['mk_selected'] = $_SESSION['sel'];
            $dt['mk_update_at'] = date("Y-m-dTH:i:s");
            $this->set($dt)->where('id_mk', $idm)->update();
            $sx .= 'Update ID:' . $idm;
        } else {
            $name = get("nameMark");
            $sx .= form_open();
            $sx .= form_input(array('name' => 'nameMark', 'class' => 'form-control', 'value' => $name));
            $sx .= form_submit(array('name' => 'action', 'value' => lang('brapci.save')));
            $sx .= form_close();

            if ((get("nameMark") != '') and (get("action") != '')) {
                $dt['mk_user'] = $_SESSION['id'];
                $dt['mk_name'] = get("nameMark");
                $dt['mk_selected'] = $_SESSION['sel'];
                $dt['mk_update_at'] = date("Y-m-dTH:i:s");
                $idm = $this->set($dt)->insert();
                $_SESSION['markName'] = $idm;

                $sx .= 'Saved "'.$dt['mk_name'].'"';
            }
        }
        $sx = bs(bsc($sx, 12));
        return $sx;
    }
}
