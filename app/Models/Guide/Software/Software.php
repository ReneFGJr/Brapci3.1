<?php

namespace App\Models\Guide\Software;

use CodeIgniter\Model;

class Software extends Model
{
    protected $DBGroup          = 'software';
    protected $table            = 'software';
    protected $primaryKey       = 'id_s';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_s ',
        's_name',
        's_description',
        's_url',
        's_version',
        'created_at'
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

    function saveStep($dt)
        {
            //pre($dt);
            $SoftwareSteps = new \App\Models\Guide\Software\SoftwareSteps();
            if ($dt['id_st'] != '') {
                $SoftwareSteps->set($dt)->where('id_st',$dt['id_st'])->update();;
            } else {
                $SoftwareSteps->set($dt)->insert();
            }
            return '<meta http-equiv="refresh" content="0; url=' . site_url('/guide/software/view/' . $dt['st_software']) . '">';
        }

    function createSteps($id)
        {
        $SoftwareSteps = new \App\Models\Guide\Software\SoftwareSteps();
        $dt = [];

        $ord = $SoftwareSteps->selectMax('st_order')->where('st_software', $id)->first();
        $ord = $ord['st_order'] + 1;

            $dt['title'] = 'Create New Software';
            $dt['form_action'] = site_url('/guide/software/saveStep');
            $dt['form_method'] = 'post';
            $dt['step'] = [
                'id_st' => '',
                'st_software' => $id,
                'st_order' => $ord,
                's_name' => '',
                's_description' => '',
                's_url' => '',
                's_version' => '',
                'st_so' => '1',
                'created_at' => date('Y-m-d H:i:s')
            ];

        $sx = view('Software/SoftwareStepsEdit', $dt);
        return $sx;
        }

    function createSoftware($id)
    {
        $dt = [];

        if ($id == 0 or $id == '' ) {
            $dt['title'] = 'Create New Software';
            $dt['form_action'] = site_url('/guide/software/save');
            $dt['form_method'] = 'post';
            $dt['software'] = [
                'id_s' => '',
                's_name' => '',
                's_description' => '',
                's_url' => '',
                's_version' => '',
                'created_at' => date('Y-m-d H:i:s')
            ];
        } else {
            $dt['software'] = $this->find($id);
            $dt['title'] = $dt['software']['s_name'] . ' - Edit Software';
            $dt['form_action'] = site_url('/guide/software/save');
            $dt['form_method'] = 'post';

        }

        $sx = view('Software/SoftwareEdit', $dt);
        return $sx;
    }

    function viewSoftware($id = '')
    {
        $d = $this->select('id_s, s_name, s_description, s_url, s_version, created_at');
        $d->where('id_s', $id);
        $dt = [];
        $dt['software'] = $this->first();

        $sx = view('Software/SoftwareView', $dt);

        /*********** STEPS */
        $SoftwareSteps = new SoftwareSteps();
        $sx .=  $SoftwareSteps->viewSteps($id);

        return $sx;
    }

    function listSoftware($d1 = '', $d2 = '')
    {

        $d = $this->select('id_s, s_name, s_description, s_url, s_version, created_at');
        $dt = [];
        $dt['softwares'] = $this->findAll();

        $sx = view('Software/SoftwareList', $dt);
        return $sx;
    }
}
