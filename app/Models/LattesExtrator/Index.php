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
            if (strlen($id) == 16)
                {
                    $a = onclick(URL.'/popup/lattesextrator/?id='.$id,400,100);                    
                    $a .= bsicone('upload2',24);
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
                $file = "../.tmp/Lattes/".$id.'.xml';
                return $file;
            }

        function fileNameUpdated($id)
            {
                $file = $this->fileName($id);
                if (file_exists($file))
                    {
                        $dt = filectime($file);
                        $data = date("Ymd",$dt);
                        $now = date("Ymd");
                        if ($data == $now)
                            {
                                return true;
                            }
                    }
                return false;
            }

        function harvesting()
            {
                $id = get("id");
                if (strlen($id) == 16)
                    {
                        $filename = $this->fileName($id);
                        $url = 'https://brapci.inf.br/ws/api/?verb=lattes&q='.trim($id);
                        if (!$this->fileNameUpdated($id))
                            {
                                $data = array();
                                echo view('Brapci/Headers/header',$data);
                                $txt = file_get_contents($url);                              

                                $fileZip = '../.tmp/Zip/lattes.zip';
                                file_put_contents($fileZip,$txt);

                                $zip = new \ZipArchive();
                                $res = $zip->open($fileZip);
                                if ($res === TRUE) 
                                {
                                    $zip->extractTo('../.tmp/Lattes/');
                                    $zip->close();

                                    /***** Processar Dados */
                                    $myXMLData = file_get_contents($filename);
                                    $xml = simplexml_load_string($myXMLData);
                                    $LattesProducao = new \App\Models\LattesExtrator\LattesProducao();
                                    $LattesProducao->producao_xml($id);
                                    return wclose();
                                } else {
                                    echo bsmessage("ERRO na descompactação",3);
                                    exit;
                                }                                
                            } else {
                                echo "CACHED";
                                $LattesProducao = new \App\Models\LattesExtrator\LattesProducao();
                                $LattesProducao->producao_xml($id);
                            }
                    }
            }
}
