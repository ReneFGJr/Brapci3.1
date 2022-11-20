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

    function register($id,$tp)
        {
            $data['bug_name'] = 'Anonyminous';
            $data['bug_user'] = 0;
            $data['bug_problem'] = $tp;
            $data['bug_IP'] = ip();
            $data['bug_status'] = 1;
            $data['bug_v'] = $id;
            $this->set($data)->insert();
            return true;
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
                $erros['authors'] = 'Problema nas autorias';

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
