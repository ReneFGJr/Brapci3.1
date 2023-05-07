<?php

namespace App\Models\Dataverse;

use CodeIgniter\Model;

class Tree extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'trees';
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

    function getCollections($server,$token,$root)
        {
        $sx = '';
        $Dataverse = new \App\Models\Dataverse\Index();
        $Native = new \App\Models\Dataverse\API\Native();

        /**************** Recupera DNS *****/
        $json = $Native->getServerInfo($server, $token, $root);
        $data = json_decode($json);
        $dns = $data->data->message;

        /**************** Criar Mapeamento */
        $dir = '../.tmp/dataverse/'.$dns.'/1';
        dircheck($dir);

        /************ Recupeara Estrutura */
        $dv = [];
        $dvd = [];
        $dvn = [];
        $dv[1] = [];
        $dvd[1] = $dir;
        $idr = 1;

        while(count($dvd) > 0)
        {
        foreach($dvd as $root=>$dirt)
            {
                /**************** Root Collection */
                $dt_root = $this->readCollecttion($server,$token,$root);
                foreach($dt_root as $idv=>$name)
                    {
                        $dirtp = $dirt.'/'. $idv;
                        dircheck($dirtp);
                        $dvd[$idv] = $dirtp;
                        $dvn[$dirtp] = trim($name);
                        file_put_contents($dirtp.'/name.nm',$name);
                    }
                unset($dvd[$root]);
            }
        }
        $sx .= h('Total de '.count($dvn).' Comunidades Dataverses',5);
        return $sx;
    }

    function readCollecttion($server,$token,$root)
        {
        $Native = new \App\Models\Dataverse\API\Native();
        $json = $Native->getCollections($server,$token,$root);
        $data = json_decode($json);
        $dvn = [];
        foreach($data->data as $id=>$dvc)
            {
                $idv = $dvc->id;
                $type = $dvc->type;
                if ($type == 'dataverse')
                    {
                        $title = $dvc->title;
                        $dvn[$idv] = $title;
                    }
            }
        return $dvn;
        }
}
