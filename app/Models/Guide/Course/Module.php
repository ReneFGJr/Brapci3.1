<?php

namespace App\Models\Guide\Course;

use CodeIgniter\Model;

class Module extends Model
{
    protected $DBGroup          = 'guide';
    protected $table            = 'curso_module';
    protected $primaryKey       = 'id_cm';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_cm','cm_curso,','cm_modulo','cm_name'
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

    function view($id)
        {
            $sx = '';

            $dq = $this->find($id);
            $curso = $dq['cm_curso'];
            $mod = $dq['cm_modulo'];

            $Course = new \App\Models\Guide\Course\Index();
            $dc = $Course->le($curso);

            $idt = $dc['c_trilha'];

            $sx .= bsc(h($dc['c_nome'],2),12);
            $sx .= bsc('', 1);
            $sx .= bsc(h($dq['cm_name'], 4), 11);

            $sx .= bsc('<hr>', 11);

            $Content = new \App\Models\Guide\Course\Content();
            $sx .= $this->viewc($id);

            $sx = bs($sx);
            return $sx;
        }
    function viewc($id)
        {
            $sx = '';
            $link = '<span onclick="newwin(\''.PATH.'/guide/popup/content/'.$id.'\',1000,800);">';
            $link .= '[+]';
            $link .= '</a>';
            $sx .= bsc($link,12);
            $Content = new \App\Models\Guide\Course\Content();
            $dt = $Content
                    ->where('ct_coruse',$id)
                    ->orderBy('ct_time')
                    ->findAll();
            foreach($dt as $id=>$line)
                {
                    $sx .= bsc($line['ct_plano'], 2);
                    $sx .= bsc($line['ct_name'],4);
                    $sx .= bsc($line['ct_text'], 4);
                    $sx .= bsc($line['ct_descricao'], 4);
                }
            return $sx;

        }

    function summary($id)
        {
            $sx = '';
            $dt = $this
                ->where('cm_curso',$id)
                ->orderBy('cm_modulo')
                ->findAll();
            $sx .= bsc('módulo',1,'text-end');
            $sx .= bsc('descrição', 11);
            foreach($dt as $id=>$line)
                {
                    $link = '<a href="'.PATH. '/guide/course/module/'.$line['id_cm'].'">';
                    $linka = '</a>';
                    $sx .= bsc($line['cm_modulo'].'.',1,'text-end');
                    $sx .= bsc($link.$line['cm_name'].$linka,11);
                }
            return $sx;
        }
}
