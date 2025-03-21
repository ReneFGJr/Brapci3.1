<?php

namespace App\Models\Base;

use CodeIgniter\Model;

class V extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'vs';
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

    function v($dt)
    {
        $sx = '';
        $RDF = new \App\Models\RDF2\RDF();

        if (!is_array($dt)) {
            $dt = round($dt);
            $dt = $RDF->le($dt);
        }

        if (!isset($dt['concept']['id_cc']))
            {
                $sx = bs(bsc($RDF->E404(),12));
                return $sx;
            }

        $idc = $dt['concept']['id_cc'];
        $class = $dt['concept']['c_class'];
        $mod = COLLECTION;

        switch ($class) {
            case 'Article':
                $Work = new \App\Models\Base\Work();
                $sx = $Work->show($dt);
                break;

            case 'Proceeding':
                $Work = new \App\Models\Base\Work();
                $sx = $Work->show($dt);
                break;
            case 'Person':
                $Authority = new \App\Models\Authority\Index();
                $sx = $Authority->v($idc);
                break;

            case 'CorporateBody':
                $CorporateBody = new \App\Models\Authority\CorporateBody();
                $sx = $CorporateBody->v($idc);
                break;

            case 'Journal':
                if ($mod != '') {
                    $sx = metarefresh(PATH . '/v/' . $idc);
                    return $sx;
                }
                $Journals = new \App\Models\Base\Journals();
                $sx .= $Journals->v($dt);
                break;
            case 'Subject':
                $Subject = new \App\Models\Base\Subject();
                $sx .= $RDF->show_class($dt);
                $sx .= $Subject->v($dt);
                break;
            case 'Issue':
                $Issue = new \App\Models\Base\Issues();
                $sx .= $Issue->v($dt);
                break;
            default:
                $sx .= '<br><br><br><br>';
                $RDF = new \App\Models\RDF2\RDF();

                $sx = bs(bsc(h($class, 1) . bsmessage('Nor view')));
                $sx .= $RDF->show_class($dt);
                $sx .= $RDF->view_data($dt);
                break;
        }
        return $sx;
    }
}
