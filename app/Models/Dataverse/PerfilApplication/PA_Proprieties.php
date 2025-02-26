<?php

namespace App\Models\Dataverse\PerfilApplication;

use CodeIgniter\Model;

class PA_Proprieties extends Model
{
    protected $DBGroup          = 'dataverse';
    protected $table            = 'paproprieties';
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

    function index($d1 = '', $d2 = '', $d3 = '', $d4 = '')
    {
        $PA_Field = new \App\Models\Dataverse\PA_Field();
        $PA_Schema = new \App\Models\Dataverse\PA_Schema();
        $PA_Vocabulary = new \App\Models\Dataverse\PA_Vocabulary();

        $dt = $PA_Schema->find($d1);

        $sx = '';
        $sx .= 'metadatablock.name=' . $dt['mt_name'] . chr(10);
        $sx .= 'metadatablock.displayName=' . $dt['mt_displayName'] . chr(10);

        $df = $PA_Field->where('m_schema', $d1)->findAll();
        //pre($df);
        $vc = '';
        for ($r = 0; $r < count($df); $r++) {
            $line = $df[$r];
            $sx .= 'datasetfieldtype.' . $line['m_name'] . '.title=' . $line['m_title'] . chr(10);
            $sx .= 'datasetfieldtype.' . $line['m_name'] . '.watermark=' . $line['m_watermark'] . chr(10);
            $sx .= 'datasetfieldtype.' . $line['m_name'] . '.description=' . $line['m_description'] . chr(10);

            $dv = $PA_Vocabulary->where('vc_name', $line['m_name'])->findAll();
            for ($q = 0; $q < count($dv); $q++) {
                $lineVC = $dv[$q];
                $vc .= 'controlledvocabulary.' . $line['m_name'] . '.' . $lineVC['vc_value'] . '=' . $lineVC['vc_value'] . chr(10);
            }
        }

        $sx .= $vc;
        $vc = $sx;

        dircheck('.tmp/');
        dircheck('.tmp/dataverse/');
        dircheck('.tmp/dataverse/proprieties');
        $filename = '.tmp/dataverse/proprieties/'.$dt['mt_name'].'_br.properties';
        file_put_contents($filename, $vc);

        $sx = '<a href="'.URL.$filename.'">'.$filename.'</a>';

        $filename = '/var/www/dataverse/langBundles/'.$dt['mt_name'].'_br.properties';
        //file_put_contents($filename, $vc);
        $filename = '/var/www/dataverse/langBundles/'.$dt['mt_name'].'.properties';
        //file_put_contents($filename, $vc);

        return $sx;
    }
}
