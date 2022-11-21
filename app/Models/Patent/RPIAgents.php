<?php

namespace App\Models\Patent;

use CodeIgniter\Model;

class RPIAgents extends Model
{
    protected $DBGroup          = 'patent';
    protected $table            = 'rpi_agents';
    protected $primaryKey       = 'id_ag';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_ag','ag_name', 'ag_use',
        'ag_type','ag_country', 'ag_state',
        'ag_email','ag_url',
        'ag_notes'
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

    function import($id, $file)
    {
        $RPIPatentNR = new \App\Models\Patent\RPIPatentNR;
        $RPIPatentAgents = new \App\Models\Patent\RPIPatentAgents;
        $sx = '';
        $xml = (array)simplexml_load_file($file);

        $despacho = (array)$xml['despacho'];
        $xcode = '';
        $tot = 0;
        $names = array();
        $sx .= '<ul>';
        for ($r = 0; $r < count($despacho); $r++) {
            $reg = (array)$despacho[$r];

            /******************* PATENT PROCESSO */
            $processo = (array)$reg['processo-patente'];

            /************** RECUPERA ID PROCESSO */
            $patenteNR = (string)$processo['numero'];
            $patent = $RPIPatentNR->busca($patenteNR);
            $id_patent = $patent['id_p'];

            $dataDeposito = '1000-01-01';
            if (isset($processo['titular-lista'])) {
                $titular = (array)$processo['titular-lista'];
                if (!isset($titular[0])) {
                    $titular = array($titular);
                } else {
                    pre($titular);
                }

                for ($q=0;$q < count($titular);$q++)
                    {
                        $reg_agent = (array)$titular[$q];
                        $agent = (array)$reg_agent['titular'][$q];

                        if (!isset($agent['nome-completo']))
                            {
                                echo "OPS";
                                pre($agent);
                            }
                        $nome = $this->name_prepara($agent['nome-completo']);
                        $pais = '';
                        $estado = '';

                        if (isset($agent['endereco'])) {
                            $endereco = (array)$agent['endereco'];
                            if (!isset($endereco['pais'])) {
                                $pais = '??';
                            } else {
                                $pais = (array)$endereco['pais'];
                                $pais = $pais['sigla'];
                            }

                            if (isset($endereco['uf']))
                                {
                                    $estado = trim($endereco['uf']);
                                }

                            if ($pais == 'BR') {
                                //pre($endereco);
                                //$estado = (strimg)$endereco['uf'];
                            }

                        }
                        if (isset($nomes[$nome]))
                            {
                                $id_ag = $nomes[$nome];
                            } else {
                                $id_ag = $this->register($nome,$pais,$estado);
                                $nomes[$nome] = $id_ag;
                                $sx .= '<li>'. $nome . ' (' . trim($pais . ' ' . $estado) . ') ' . '</li>';
                            }
                        $RPIPatentAgents->register($id_patent,$id_ag,'T');

                    }
            }
            //$idp = $this->register($patenteNR);
            $tot++;
        }
        $sx .= '</ul>';
        $sx = '<p>' . bsmessage(lang('patent.found') . ' ' . $tot . ' ' . lang('patent.agents')) . '</p>' . $sx;
        $sx .= metarefresh(PATH . COLLECTION . '/proccess/' . $id, 1);
        return $sx;
    }

    function name_prepara($nome)
        {
        $nome = ascii($nome);
        $nome = troca($nome, '.', '. ');
        $nome = ucwords(strtolower($nome));
        $nome = troca($nome, '. ', '.');
        return $nome;
        }

    function le($id)
        {
            $dt = $this->where('id_ag',$id)->findAll();
            return $dt[0];
        }

    function viewtable($d1='',$d2='')
        {
            $RPIPatentAgents = new \App\Models\Patent\RPIPatentAgents;
            $this->path = PATH.COLLECTION.'/agent';
            $sx = '';
            switch($d1)
                {
                    case 'viewid':
                        $dt = $this->le($d2);
                        $dt['patents'] = $RPIPatentAgents->show_proccess($d2);
                        $sx .= view('Patent/agent',$dt);
                        break;
                    default:
                    $sx .= bs(bsc(tableview($this), 12));
                    break;
                }

            return $sx;
        }

    function register($name,$pais,$estado)
        {
            $name = substr($name,0,98);
            $dt = $this
                ->where('ag_country', $pais)
                ->where('ag_name',$name)
                ->findAll();
            if (count($dt) == 0)
                {
                    $data = array();
                    $data['ag_name'] = $name;
                    $data['ag_country'] = $pais;
                    $data['ag_state'] = $estado;
                    $this->insert($data);
                    $id = $this->getInsertID();
                } else {
                    $id = $dt[0]['id_ag'];
                }
            return $id;
        }
}
