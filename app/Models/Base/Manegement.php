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
            switch($d1)
                {
                    case 'proceedings':
                        $Works = new \App\Models\Base\Admin\Management\Work();
                        $sx .= $Works->proceedings();
                        break;
                    case 'issue':
                        $Issue = new \App\Models\Base\Admin\Management\Issue();
                        $sx .= $Issue->check();
                        break;
                    case 'work':
                        $Works = new \App\Models\Base\Admin\Management\Work();
                        $sx = $Works->check();
                        break;
                    default:
                        $sx .= 'MANEGEMENT - '.$d1;
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

            /************************************************ CRON - TAREFAS */
            $ElasticSearch = new \App\Models\ElasticSearch\Register();
            $sc = $ElasticSearch->resume();

            /************************************************ CRON - TAREFAS */
            $Lattes = new \App\Models\Api\Lattes\KtoN();
            $sc .= $Lattes->resume();




            $sx .= bsc($sb,4,'" style="border-right: 1px solid #AAA;');
            $sx .= bsc($sc, 4, '" style="border-right: 1px solid #AAA;');
            $sx .= bsc($sa,4);

            $sx = bs($sx);
            return $sx;
        }
}
