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
        //echo $url;
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
        $dt = array();
        $id = $id.get("id");

        $dt['id'] = $id;

        if (strlen($id) == 16) {

            $filename = $this->fileName($id);
            $url = 'https://brapci.inf.br/ws/api/?verb=lattes&q=' . trim($id);
            if (!$this->fileNameUpdated($id)) {
                $data = array();
                echo 'Harvesting '.$id.cr();
                $txt = file_get_contents($url);
                dircheck("../.tmp");
                dircheck("../.tmp/Zip");
                $fileZip = '../.tmp/Zip/lattes.zip';

                if (strlen($txt) == 0)
                    {
                        echo "ERRO: o arquivo está vazio";
                        exit;
                    }

                file_put_contents($fileZip, $txt);

                $type = mime_content_type($fileZip);
                if ($type == 'application/json')
                    {
                        echo "ERRO";
                        $txt = file_get_contents($fileZip);
                        $dt = (array)json_decode($txt);
                        echo ' '.$dt['erro'];
                        echo '<br>' . $dt['description'];
                    }

                $zip = new \ZipArchive();
                $res = $zip->open($fileZip);
                if ($res === TRUE) {
                    $zip->extractTo('../.tmp/Lattes/');
                    $zip->close();

                    if (!file_exists($filename))
                        {
                            echo "Erro ao abrir o arquivo ".$filename;
                            exit;
                        }
                } else {
                    echo bsmessage("ERRO na descompactação em $fileZip", 3);
                    exit;
                }
            } else {
                echo "CACHED";
            }

            /***** Processar Dados */
            $myXMLData = file_get_contents($filename);
            $xml = simplexml_load_string($myXMLData);

            $LattesDados = new \App\Models\LattesExtrator\LattesDados();
            $LattesDados->dados_xml($id);

            $LattesProducao = new \App\Models\LattesExtrator\LattesProducao();
            $LattesProducao->producao_xml($id);

            $LattesProducaoEvento = new \App\Models\LattesExtrator\LattesProducaoEvento();
            $LattesProducaoEvento->producao_xml($id);

            $LattesProducaoLivro = new \App\Models\LattesExtrator\LattesProducaoLivro();
            $LattesProducaoLivro->producao_xml($id);

            $LattesProducaoCapitulo = new \App\Models\LattesExtrator\LattesProducaoCapitulo();
            $LattesProducaoCapitulo->producao_xml($id);

            $LattesOrientacao = new \App\Models\LattesExtrator\LattesOrientacao();
            $LattesOrientacao->orientacao_xml($id);
            return wclose();
        }
        return $dt;
    }
}