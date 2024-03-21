<?php

namespace App\Models\Base;

use CodeIgniter\Model;

class Manegement extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'managements';
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

    function index($d1,$d2,$d3)
        {
            $sx = '';
            echo h($d2);

            switch($d1)
                {
                    default:
                        $sx .= $this->painel();
                        $sx .= $this->subpainel();
                        break;
                }
            return $sx;
        }

    function subpainel()
        {
            $menu['#Check'] = 3;
            $menu[PATH. 'admin/source'] = 'Journals';
            $menu[PATH . 'admin/issue'] = 'Issue';
            $menu[PATH . 'admin/section'] = 'Sections';
            $menu[PATH . 'admin/person'] = 'Person';

            $sx = menu($menu);

            $sx = bs($sx);
            return $sx;
        }

    function painel()
        {
            $sx = '';
            /************************************************ CRON - TAREFAS */
            $Export = new \App\Models\Base\Export();
            $sa = $Export->resume();

            /************************************************ CRON - TAREFAS */
            $Sources = new \App\Models\Base\Sources();
            $sb = $Sources->resume();

            /****************************************** CRON - ElasticSearch */
            $ElasticSearch = new \App\Models\ElasticSearch\Register();
            $sc = $ElasticSearch->resume();

            $Books = new \App\Models\Base\Book();
            $sc .= $Books->resume();

            /************************************************ CRON - OAIPMH */
            $Oaipmh = new \App\Models\Oaipmh\Index();
            $sd = $Oaipmh->resume();

            $OS = new \App\Models\Functions\OS();
            $sd .= $OS->resume();

            /************************************************ CRON - TAREFAS */
            $Lattes = new \App\Models\Lattes\Kto16();
            $sc .= $Lattes->resume();

            $sx .= bsc($sb,3,'" style="border-right: 1px solid #AAA;');
            $sx .= bsc($sc,3,'" style="border-right: 1px solid #AAA;');
            $sx .= bsc($sd,3,'" style="border-right: 1px solid #AAA;');
            $sx .= bsc($sa,3);

            $sx = bs($sx);
            return $sx;
        }
}
