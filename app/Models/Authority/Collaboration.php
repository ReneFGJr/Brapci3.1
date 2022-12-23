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

            $this->check($dt['a_brapci']);
            echo "processado ".$dt['a_brapci'];
        }

    function register($w,$a)
        {
            $dt = $this
                ->where('ca_work',$w)
                ->where('ca_autho',$a)
                ->findAll();

            if (count($dt) == 0)
                {
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
                    $this->register($line['d_r1'],$line['d_r2']);
                }
            }
        }

    function check($id)
        {
            $tot = 0;
            $RDF = new \App\Models\RDF\Rdf();
            $dt = $RDF->le($id);
            for ($r=0;$r < count($dt['data']);$r++)
                {
                    $line = $dt['data'][$r];
                    $class = $line['c_class'];
                    if ($class == 'hasAuthor')
                        {
                            $this->check_work($line['d_r1']);
                            $tot++;
                        }
                }
                $sx = 'novos = '.$tot;
                return $sx;
        }

    function collaborations($id)
        {
            $dt = $this->select('count(*) as total, ca_autho')
                ->join('collaboration as coll2','collaboration.ca_work = coll2.ca_work')
                ->where('ca_auth',$id)
                ->findAll();
            echo $this->getlastquery();
            pre($dt);

        }
}
