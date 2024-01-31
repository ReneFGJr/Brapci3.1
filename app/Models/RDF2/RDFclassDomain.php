<?php

namespace App\Models\RDF2;

use CodeIgniter\Model;

class RDFclassDomain extends Model
{
    protected $DBGroup          = 'rdf2';
    protected $table            = 'rdf_class_domain';
    protected $primaryKey       = 'id_cd';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'cd_property', 'cd_domain','cd_range'
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

    function rules()
        {
            $cp = '*';
            $cp = 'C1.c_class as domain';
            $cp .= ', C2.c_class as prop';
            $cp .= ', C3.c_class as range';
            $dt = $this
                ->select($cp)
                ->join('brapci_rdf.rdf_class as C1','C1.id_c = cd_domain', 'left')
                ->join('brapci_rdf.rdf_class as C2', 'C2.id_c = cd_property','left')
                ->join('brapci_rdf.rdf_class as C3','C3.id_c = cd_range', 'left')
                ->orderBy('C1.c_class,C2.c_class,C3.c_class')
                ->findAll();

            $sx = '<table class="table full">';
            $sx .= '<tr>
                    <th witth="33%">Domain</th>
                    <th witth="33%">Propriety</th>
                    <th witth="33%">Range</th>
                    </tr>
                    ';
            foreach($dt as $id=>$line)
                {
                    $sx .= '<tr>';
                    $sx .= '<td>'.$line['domain'].'</td>';
                    $sx .= '<td>' . $line['prop'] . '</td>';
                    $sx .= '<td>' . $line['range'] . '</td>';
                    $sx .= '</tr>';
                }
            $sx .= '</table>';
            return bs(bsc($sx,12));
        }

    function register($class, $prop, $range)
    {
        $this->where('cd_property', $prop);
        $this->where('cd_domain', $class);
        $this->where('cd_range', $range);
        $dt = $this->first();
        if ($dt == null) {
            $d = [];
            $d['cd_property'] = $prop;
            $d['cd_domain'] = $class;
            $d['cd_range'] = $range;

            return $this->set($d)->insert();
        } else {
            return $dt['id_cd'];
        }
    }

    function listDomain($id)
        {
            $dt = $this
                ->join('rdf_class', 'cd_domain = id_c')
                ->where('cd_property',$id)
                ->findAll();
            return $dt;
        }
}
