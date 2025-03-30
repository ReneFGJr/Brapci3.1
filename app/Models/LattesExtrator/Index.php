<?php

namespace App\Models\LattesExtrator;

use CodeIgniter\Model;

class Index extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'indices';
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

    function btn_coletor($id)
    {
        if (strlen($id) == 16) {
            $a = onclick(URL . '/popup/lattesextrator/?id=' . $id, 400, 100);
            $a .= bsicone('upload2', 24);
            $a .= '</span>';
        }
        return $a;

        //$url = 'https://brapci.inf.br/ws/api/?verb=lattesdata&q='.$id;
        //$sx .= $url;
    }

    function fileName($id)
    {
        dircheck('../.tmp');
        dircheck('../.tmp/Lattes/');
        dircheck('../.tmp/Zip/');
        $file = "../.tmp/Lattes/" . $id . '.xml';
        return $file;
    }

    function fileNameUpdated($id)
    {
        $file = $this->fileName($id);
        if (file_exists($file)) {
            $dt = filectime($file);
            $data = date("Ymd", $dt);
            $now = date("Ymd");
            if ($data == $now) {
                return true;
            }
        }
        return false;
    }

    function harvesting($id='')
    {
        $sx = '';
        $dt = array();
        $id = $id.get("id");

        $dt['id'] = $id;

        if (strlen($id) == 16) {

            $filename = $this->fileName($id);
            if (!file_exists($filename))
                {

                } else {
                    $dt = filectime($filename);
                    $data = date("Ymd", $dt);
                    $now = date("Ymd");
                    if ($data == $now) {
                        return 'Arquivo já existe!';
                    }
                }
            pre($filename);

            /***** Processar Dados */
            $myXMLData = file_get_contents($filename);
            $xml = simplexml_load_string($myXMLData);

            $LattesDados = new \App\Models\LattesExtrator\LattesDados();
            $LattesEndereco = new \App\Models\LattesExtrator\LattesEndereco();
            $LattesFormacao = new \App\Models\LattesExtrator\LattesFormacao();
            $LattesProducao = new \App\Models\LattesExtrator\LattesProducao();
            $LattesProducaoEvento = new \App\Models\LattesExtrator\LattesProducaoEvento();
            $LattesProducaoLivro = new \App\Models\LattesExtrator\LattesProducaoLivro();
            $LattesProducaoCapitulo = new \App\Models\LattesExtrator\LattesProducaoCapitulo();
            $LattesOrientacao = new \App\Models\LattesExtrator\LattesOrientacao();
            $LattesProducaoTecnica = new \App\Models\LattesExtrator\LattesProducaoTecnica();
            $LattesProducaoArtistica = new \App\Models\LattesExtrator\LattesProducaoArtistica();

            $sx .= 'Zerando ... '.date("Y-m-d H:i:s").'<br>';
            $LattesDados->zerezima_dados_xml($id);
            $LattesEndereco->zerezima_dados_xml($id);
            $LattesFormacao->zerezima_dados_xml($id);
            $LattesProducao->zerezima_dados_xml($id);
            $LattesProducaoEvento->zerezima_dados_xml($id);
            $LattesProducaoLivro->zerezima_dados_xml($id);
            $LattesProducaoCapitulo->zerezima_dados_xml($id);
            $LattesOrientacao->zerezima_dados_xml($id);
            //$LattesProducaoTecnica->zerezima_dados_xml($id);
            $LattesProducaoArtistica->zerezima_dados_xml($id);


            $sx .= 'Importando' . date("Y-m-d H:i:s") . '<br>';
            $LattesDados->dados_xml($id);
            $sx .= 'LattesEndereco ... ' . date("Y-m-d H:i:s") . '<br>';
            $LattesEndereco->dados_xml($id);
            $sx .= 'LattesFormacao ... ' . date("Y-m-d H:i:s") . '<br>';
            $LattesFormacao->dados_xml($id);
            $sx .= 'LattesProducao ... ' . date("Y-m-d H:i:s") . '<br>';
            $LattesProducao->producao_xml($id);
            $sx .= 'LattesProducaoEvento ... ' . date("Y-m-d H:i:s") . '<br>';
            $LattesProducaoEvento->producao_xml($id);
            $sx .= 'LattesProducaoLivro ... ' . date("Y-m-d H:i:s") . '<br>';
            $LattesProducaoLivro->producao_xml($id);
            $sx .= 'LattesProducaoCapitulo ... ' . date("Y-m-d H:i:s") . '<br>';
            $LattesProducaoCapitulo->producao_xml($id);
            $sx .= 'LattesOrientacao ... ' . date("Y-m-d H:i:s") . '<br>';
            $LattesOrientacao->orientacao_xml($id);
            $sx .= 'LattesProducaoTecnica ... ' . date("Y-m-d H:i:s") . '<br>';
            //$LattesProducaoTecnica->producao_xml($id);
            $sx .= 'LattesProducaoArtistica ... ' . date("Y-m-d H:i:s") . '<br>';
            $LattesProducaoArtistica->producao_xml($id);
            $sx = '<tt>'.$sx.'</tt>';
            return $sx.wclose();
        }
        return $sx;
    }
}
