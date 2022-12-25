<?php

namespace App\Models\Authority;

use CodeIgniter\Model;

class Collaboration extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'brapci_authority.collaborations';
    protected $primaryKey       = 'id_ca';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_ca', 'ca_autho', 'ca_collab', 'ca_work'
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

    function check_next()
    {
        $Authoriry = new \App\Models\Authority\AuthorityNames();
        $dt = $Authoriry->orderby('updated_at')->first();

        $idb = $dt['a_brapci'];
        $this->check($idb);
        $data['updated_at'] = date("Y-m-d H:m:s");
        $Authoriry->set($data)->where('a_brapci', $idb)->update();
        echo "processado Authority Collaboration" . $idb;
    }

    function register($w, $a)
    {
        $dt = $this
            ->where('ca_work', $w)
            ->where('ca_autho', $a)
            ->findAll();

        if (count($dt) == 0) {
            $dt['ca_work'] = $w;
            $dt['ca_autho'] = $a;
            $this->insert($dt);
        }
    }

    function check_work($id)
    {
        $RDF = new \App\Models\RDF\Rdf();
        $dt = $RDF->le($id);
        for ($r = 0; $r < count($dt['data']); $r++) {
            $line = $dt['data'][$r];
            $class = $line['c_class'];
            if ($class == 'hasAuthor') {
                $this->register($line['d_r1'], $line['d_r2']);
            }
        }
    }

    function check($id)
    {
        $tot = 0;
        $RDF = new \App\Models\RDF\Rdf();
        $dt = $RDF->le($id);
        for ($r = 0; $r < count($dt['data']); $r++) {
            $line = $dt['data'][$r];
            $class = $line['c_class'];
            if ($class == 'hasAuthor') {
                $this->check_work($line['d_r1']);
                $tot++;
            }
        }
        $sx = 'novos = ' . $tot;
        return $sx;
    }

    function collaborations($id)
    {
        $sx = '';
        $sql = "
                select count(*) as total, ca_autho, id_a, a_prefTerm from (
                SELECT ca_work as w FROM " . $this->table . " WHERE `ca_autho` = $id
                ) as works
                INNER JOIN " . $this->table . " ON W = ca_work
                LEFT JOIN brapci_authority.authoritynames ON ca_autho = a_brapci
                group by ca_autho, id_a, a_prefTerm
                order by total desc, a_prefTerm
           ";
        $dt = (array)$this->query($sql)->getResult();;
        for ($r = 0; $r < count($dt); $r++) {
            $line = (array)$dt[$r];


            $link = '<a href="' . URL . '/v/' . $line['ca_autho'] . '">';
            $linka = '</a>';

            $name = trim($line['a_prefTerm']);
            if ($name == '') {
                $name .= '<i>not proccessed</i>';
            }
            $sx .= $link . $name . $linka;

            $sx .= ' ';
            $sx .= '('.$line['total'].')';
            $sx .= '<br>';
        }
        echo $sx;
    }
}
