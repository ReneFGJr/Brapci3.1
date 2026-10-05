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
        'bug_solution', 'bug_url', 'bug_description', 'updated_at'
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
                        $request = service('request');
                        if (strtoupper($request->getMethod()) === 'POST' && ctype_digit((string) $d2)) {
                            service('security')->verify($request);
                            $solution = $request->getPost('solution');
                            if (is_string($solution) && mb_strlen(trim($solution)) <= 2000) {
                                $this->corrected((int) $d2, trim($solution));
                            }
                        }
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

    function corrected($id, $solution = '')
        {
            $data['bug_status'] = 2;
            $data['updated_at'] = date("Y-m-d H:i:s");
            $data['bug_solution'] = $solution !== '' ? $solution : 'Fixed';
            $this->set($data)->where('id_bug',$id)->where('bug_status', 1)->update();
            return "";
        }

    function register($id,$tp)
        {
            $Socials = new \App\Models\Socials();
            $token = get("user");
            $user = $Socials->validToken($token);
            if ($user != [])
                {
                    $userID = $user['ID'];
                    $userName = $user['user'];
                } else {
                    $userID = 0;
                    $userName = 'Anonyminous';
                }

            $dt = $this
                ->where('bug_v',$id)
                ->where('bug_problem', $tp)
                ->where('bug_status', 1)
                ->findAll();

            if (count($dt) == 0)
            {
                $data['bug_name'] = $userName;
                $data['bug_user'] = $userID;
                $data['bug_problem'] = $tp;
                $data['bug_IP'] = ip();
                $data['bug_status'] = 1;
                $data['bug_v'] = $id;
                $this->set($data)->insert();
                $data['message'] = 'Item registrado com sucesso';
                $data['status'] = '200';
            } else {
                $data['message'] = 'Esse pedido já foi registrado';
                $data['status'] = '202';
            }
            return $data;
        }

    function list()
    {
        $request = service('request');
        $status = $request->getGet('status');
        $status = in_array($status, ['1', '2', 'all'], true) ? $status : '1';
        $problem = $request->getGet('problem');
        $problem = is_string($problem) ? $problem : '';
        $search = $request->getGet('q');
        $search = is_string($search) ? mb_substr(trim($search), 0, 200) : '';
        $page = max(1, (int) $request->getGet('page'));
        $counts = (new self())->select('bug_status, COUNT(*) AS total')->groupBy('bug_status')->findAll();
        $totals = [1 => 0, 2 => 0];
        foreach ($counts as $count) {
            $totals[(int) $count['bug_status']] = (int) $count['total'];
        }
        $types = (new self())->select('bug_problem')->distinct()->orderBy('bug_problem')->findAll();
        if ($status !== 'all') {
            $this->where('bug_status', (int) $status);
        }
        if ($problem !== '') {
            $this->where('bug_problem', $problem);
        }
        if ($search !== '') {
            $this->groupStart()->like('bug_name', $search)->orLike('bug_problem', $search)
                ->orLike('bug_v', $search)->orLike('id_bug', $search)->groupEnd();
        }
        $total = $this->countAllResults(false);
        $pages = max(1, (int) ceil($total / 25));
        $page = min($page, $pages);
        $reports = $this->orderBy('id_bug', 'DESC')->findAll(25, ($page - 1) * 25);
        helper('form');
        return bs(bsc(view('Admin/bugs', compact('reports', 'totals', 'types', 'status', 'problem', 'search', 'page', 'pages', 'total')), 12));
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
            $sx .= bsicone('bug',28);
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
