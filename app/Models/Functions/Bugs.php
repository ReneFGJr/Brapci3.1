<?php

namespace App\Models\Functions;

use CodeIgniter\Model;

class Bugs extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'bugs';
    protected $primaryKey       = 'id_bug';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'bug_name', 'bug_user', 'bug_problem',
        'bug_IP', 'bug_status', 'bug_v',
        'bug_solution'
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

    var $version = 'BUG Report 1.0';

    function index($d1,$d2,$d3)
        {
            $sx = '';
            switch($d1)
                {
                    case 'corrected':
                        $d2 = round($d2);
                        $this->corrected($d2);
                        $sx .= $this->list();
                        break;
                    default:
                        $sx .= $this->list();
                        break;
                }
            return $sx;
        }

    function resume()
        {
            $dt = $this->
                select('count(*) as total, bug_problem')
                ->where('bug_status',1)
                ->groupBy('bug_problem')
                ->orderBy('bug_problem')
                ->findAll();
            $sx = '<ul>';
            foreach($dt as $id=>$line)
                {
                    $sx .= '<li>'.lang('brapci.'.$line['bug_problem']).' '.$line['total'].'</li>';
                }
            $sx .= '</ul>';
            return $sx;
        }

    function corrected($id)
        {
            $data['bug_status'] = 2;
            $data['updated_at'] = date("Y-m-d H:i:s");
            $data['bug_solution'] = 'Fixed';
            $this->set($data)->where('id_bug',$id)->update();
            return "";
        }

    function register($id,$tp)
        {
            $dt = $this
                ->where('bug_v',$id)
                ->where('bug_problem', $tp)
                ->where('bug_status', 1)
                ->findAll();
            if (count($dt) == 0)
            {
                $data['bug_name'] = 'Anonyminous';
                $data['bug_user'] = 0;
                $data['bug_problem'] = $tp;
                $data['bug_IP'] = ip();
                $data['bug_status'] = 1;
                $data['bug_v'] = $id;
                $this->set($data)->insert();
            }
            return true;
        }

    function list()
        {
            $RDF = new \App\Models\Rdf\RDF();
            $sx = '';
            $dt = $this
                ->where('bug_status',1)
                ->orderBy('bug_problem,bug_name,id_bug')
                ->findAll();
            $xt = '';
            $sx .= '<ul>';
            foreach($dt as $id=>$line)
                {
                    $t = $line['bug_problem'];
                    if ($xt != $t)
                        {
                            $link = '<a href="'.PATH.'ai/authority/'.$t.'">';
                            $linka = '</a>';
                            $sx .= $link.h($t,4).$linka;
                            $xt = $t;
                        }
                    $name = '<a href="'.PATH.'/v/'.$line['bug_v'].'" target="_blank">'.$line['bug_v'].'</a>';
                    echo "OK";
                    $chk_ok = '<a class="ms-3" title="'.lang('brapci.corrected').'" href="' . PATH . '/admin/bugs/corrected/'.$line['id_bug'].'">'.bsicone('to_check').'</a>';
                    //$content = '<div style="font-size: 0.8em;">' . $RDF->c($line['bug_v']) .$chk_ok. '</div>';
                    $content = $line['bug_v'].$chk_ok;
                    $sx .= '<li>'.$content.'</li>'.cr();
                }
            $sx .= '</ul>';
            $sx = bs(bsc($sx,12));
            return $sx;
        }

    function recoverProblem($type)
        {
            $dt = $this
                ->where('bug_problem',$type)
                ->where('bug_status', 1)
                ->findAll();
            return $dt;
        }

    function show($id)
        {
            $action = $this->form_bug($id);
            $sx = '';
            $sx .= '<button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#bugModal" style="width: 100%;">';
            $sx .= '<img src="'.URL.('/img/icons/bug.png').'" width="25">';
            $sx .= ' Problemas ?';
            $sx .= '</button>';

            $sx .= '
               <!-- Modal -->
                <div class="modal fade" id="bugModal" tabindex="-1" aria-labelledby="bugModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="bugModalLabel"><b>'.lang('brapci.bug_report'). '</b></h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="'.lang('brapci.close'). '"></button>
                    </div>
                    <div class="modal-body" class="bug_report">
                        '.$action.'
                    </div>
                    <div class="modal-footer">
                        '.$this->version.'
                    </div>
                    </div>
                </div>
                </div>';
            return $sx;
        }

        function form_bug($id)
            {
                $sx = h('AI - '.lang('brapci.bug_report'),4);

                $erros = array();
                $erros['pdf_not'] = 'PDF não disponível';
                $erros['pdf_err'] = 'PDF incorreto';
                $erros['abstract'] = 'Problema no resumo';
                $erros['key'] = 'Problema nas palavras chave';
                $erros['title'] = 'Problema no título';
                $erros['authors'] = 'Problema no nome do(s) autor(es)';

                $sx .= '<div class="form-check">';
                $sx .= '<input type="hidden" name="idc" id="idc" value="'.$id.'">';
                foreach($erros as $path=>$label)
                {
                   $sx .= '<input class="form-check-input" type="radio" name="ebug" id="ebug" value="'. $path.'">';
                    $sx .= '<label class="form-check-label" for="bug_'.$path.'">';
                    $sx .= $label;
                    $sx .= '</label>';
                    $sx .= '<br>';
                }
                $sx .= '</div>';

                $sx .= '<button type="button" onclick="bug_report();" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#bugModal" style="width: 100%;">';
                $sx .= 'Comunicar o erro';
                $sx .= '</button>';


                return $sx;
            }
}
