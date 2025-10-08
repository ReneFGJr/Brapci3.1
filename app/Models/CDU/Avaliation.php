<?php

namespace App\Models\CDU;

use CodeIgniter\Model;

class Avaliation extends Model
{
    protected $DBGroup          = 'CDU';
    protected $table            = 'avaliation';
    protected $primaryKey       = 'id_av';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_av',
        'av_student',
        'av_q1',
        'av_q2',
        'av_q3',
        'av_q4',
        'av_q5',
        'av_q6',
        'av_q7',
        'av_q8',
        'av_q9',
        'av_q10',
        'av_q11',
        'av_q12',
        'av_q13',
        'av_q14',
        'av_q15',
        'av_q16',
        'q_used'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

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

    function makeTest($cracha)
        {
            $Questions = new \App\Models\CDU\Questions();
            $dt = $this->where('av_student',$cracha)->first();
            if ($dt == []) {

                $gr = [];
            $gr[1] = 1;
            $gr[2] = 1;
            $gr[3] = 1;
            $gr[4] = 1;
            $gr[5] = 1;
            $gr[6] = 1;
            $gr[7] = 1;
            $gr[8] = 1;
            $gr[9] = 1;
            $gr[10] = 2;
            $gr[12] = 2;
            $gr[13] = 2;
            $gr[15] = 2;
            $gr[16] = 2;
                /*
                $gr[11] = 2;
                $gr[4] = 1;
                $gr[6] = 1;
                $gr[7] = 1;
                $gr[8] = 1;
                $gr[11] = 1;
                $gr[12] = 1;
                $gr[13] = 1;
                $gr[14] = 1;
                $gr[15] = 1;
                $gr[16] = 1;
                $gr[17] = 1;
                $gr[18] = 1;
                $gr[19] = 1;
                */
                $i = 0;
                $av = [];
                $av['av_student'] = $cracha;
                foreach ($gr as $k => $v) {
                    $q = $Questions->where('q_group', $k)->orderBy('q_used')->findAll($v);
                    foreach ($q as $d) {
                        $di = [];
                        $di['q_used'] = $d['q_used'] + 1;
                        $Questions->set($di)->where('id_q',$d['id_q'])->update();
                        $i++;
                        $av['av_q' . $i] = $d['id_q'];
                    }
                }
                $id = $this->set($av)->insert();
                $dt = $this->where('av_student', $cracha)->first();
            }
            return $dt;
        }

        function showTest($cracha)
        {
            $sx = '<div class="container">';
            $sx .= '<div class="row">';
            $Questions = new \App\Models\CDU\Questions();
            $dt = $this->where('av_student',$cracha)->first();
            if ($dt == []) {
                return 'NOK';
            } else {
                $sx .= '<h3>Avaliação</h3>';
                for ($i = 1; $i <= 16; $i++) {
                    if (isset($dt['av_q' . $i])) {
                        $q = $Questions->where('id_q', $dt['av_q' . $i])->first();
                        if ($q != []) {
                            $txt = troca($q['q_ask'], chr(13), '<br>');
                            $txt = troca($q['q_ask'], chr(10), '<br>');
                            $sx .= '<div class="col-12 mt-4">';
                            $sx .= '<h4>Questão '.$i.' - '. $q['q_statement'].'</h4>';
                            $sx .= 'No Moodle explique como você chegou a essa resposta, em detalhes (quais instrumentos consultou, como utilizou, quais os termos identificados).<br>';
                            $sx .= '<p style="font-size: 22px; color: blue;">'.$txt.'</p>';
                           // $sx .= '<p style="color: green">Resposta: '.$q['q_comentary'].'</p>';
                            $sx .= '</div>';
                        }
                    }
                }
            }
            $sx .= '</div>';
            $sx .= '</div>';
            return $sx;
        }
}
