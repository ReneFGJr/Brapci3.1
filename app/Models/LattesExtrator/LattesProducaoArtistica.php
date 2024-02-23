<?php

namespace App\Models\LattesExtrator;

use CodeIgniter\Model;

class LattesProducaoArtistica extends Model
{
    protected $DBGroup          = 'lattes';
    protected $table            = 'lattesproducao_artistica';
    protected $primaryKey       = 'id_la';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields        = [
        'id_la ', 'la_author', 'la_brapci_rdf',
        'la_authors', 'la_title', 'la_ano',
        'la_url', 'la_doi', 'la_issn',
        'la_pais', 'la_vol', 'la_nr',
        'la_place', 'la_natureza', 'la_seq',
        'la_setor'
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

    function selo($pa)
    {
        $sx = '';
        if ($pa == [])
            {
                $pa['Produção Artística'] = 0;
            }
        foreach ($pa as $desc => $total) {
            $sa = '<div class="text-center p-1 mb-3" style="width: 100%; border: 1px solid #000; border-radius: 10px;  line-height: 80%;">';
            $sa .= '<span style="font-size: 16px;">' . $total . '</span>';
            $sa .= '<br>';
            $sa .= '<b style="font-size: 12px; ">' . $desc . '</b>';
            $sa .= '</div>';
            $sx .= bsc($sa, 2);
        }
        return $sx;
    }

    function resume($id, $type = 'Z')
    {
        $dt = $this->select('count(*) as total, la_author, nt_name')
            ->join('lattes_natureza', 'la_natureza = id_nt')
            ->where('la_author', $id)
            ->groupBy('la_author, nt_name')
            ->orderBy('nt_name')
            ->findAll();

        $rst = [];
        foreach ($dt as $line) {
            $name = $line['nt_name'];
            $total = $line['total'];
            $rst[$name] = $total;
        }
        return $rst;
    }

    function zerezima_dados_xml($id)
    {
        $this->where('la_author', $id)->delete();
        return true;
    }

    function csv($id)
    {
        $Setores = new \App\Models\LattesExtrator\LattesSetores();
        set_time_limit(0);
        $cp = 'la_author, la_authors, la_title, la_ano, la_doi, la_issn, nt_name as la_natureza, la_pais, la_setor';
        $dt = $this
            ->select($cp)
            ->join('brapci_tools.projects_harvesting_xml', 'la_author  = hx_id_lattes')
            ->join('lattes_natureza', 'la_natureza = id_nt')
            ->where('hx_project', $id)
            ->orderBy('la_seq')
            ->findAll();

            //echo $this->getlastquery();

        header("Content-Type: text/csv");
        header("Content-Disposition: attachment; filename=brapci_tools_artistica_" . date("Ymd-His") . ".csv");
        header("Pragma: no-cache");
        header("Expires: 0");

        echo 'IDLATTES,AUTHORS,TITLE,YEAR,DOI,ISSN,PAIS,NATUREZA,SETOR' . chr(13);

        foreach ($dt as $id => $line) {
            $sa = '';
            $sa .= '"' . $line['la_author'] . '",';
            $sa .= '"' . $line['la_authors'] . '",';
            $sa .= '"' . $line['la_title'] . '",';
            $sa .= '"' . $line['la_ano'] . '",';
            $sa .= '"' . $line['la_doi'] . '",';
            $sa .= '"' . $line['la_issn'] . '",';
            $sa .= '"' . $line['la_pais'].'",';
            $sa .= '"' . $line['la_natureza'] . '",';
            $sa .= '"' . $Setores->show($line['la_setor']). '",';
            $sa = troca($sa, chr(13), '');
            $sa = troca($sa, chr(10), '');
            $sx = $sa . chr(13);
            echo $sx;
        }
        exit;
    }

    function producao($id)
    {
        $tela = '';
        $dt = $this
            ->join('lattes_natureza', 'la_natureza = id_nt')
            ->where('la_author', $id)
            ->orderBy('nt_name, la_ano desc, la_seq ')->findAll();
        $tela .= '<ol>';
        $xcat = '';
        foreach ($dt as $id => $line) {
            $cat = $line['nt_name'];
            if ($cat != $xcat) {
                if ($xcat != '') { $tela .= '</ol><ol>';}
                $tela .= h('<b>'.$cat. '</b>', 4);
                $xcat = $cat;
            }
            $tela .= '<li class="small">' . $line['la_authors'] . '. ' . $line['la_title'] . '. ';

            $tela .= ', ' . $line['la_ano'];
            $tela .= '</li>';
        }
        $tela .= '</ol>';
        return $tela;
    }

    function producao_xml($id)
    {
        $Lang = new \App\Models\Language\Lang();
        $Natureza = new \App\Models\LattesExtrator\LattesNatureza();
        $SetorAtividade = new \App\Models\LattesExtrator\LattesSetores();
        $LattesExtrator = new \App\Models\LattesExtrator\Index();
        $file = $LattesExtrator->fileName($id);
        if (!file_exists($file)) {
            echo "ERRO NO ARQUIVO " . $file;
            exit;
        }
        $xml = (array)simplexml_load_file($file);

        $xml = (array)$xml;

        if (isset($xml['OUTRA-PRODUCAO'])) {
            $prod = (array)$xml['OUTRA-PRODUCAO'];
            if (isset($prod['PRODUCAO-ARTISTICA-CULTURAL'])) {
                $art = (array)$prod['PRODUCAO-ARTISTICA-CULTURAL'];
                if (isset($art['OUTRA-PRODUCAO-ARTISTICA-CULTURAL'])) {
                    $art = (array)$art['OUTRA-PRODUCAO-ARTISTICA-CULTURAL'];

                    foreach ($art as $idx => $line) {

                        //pre($line);


                        $line = (array)$line;
                        if (isset($line['@attributes'])) {
                            $attr = $line['@attributes'];

                            if (isset($line['DADOS-BASICOS-DE-OUTRA-PRODUCAO-ARTISTICA-CULTURAL'])) {
                                $dados = (array)$line['DADOS-BASICOS-DE-OUTRA-PRODUCAO-ARTISTICA-CULTURAL'];
                                $dados = (array)$dados['@attributes'];


                                $p = array();
                                $p['la_author'] = $id;
                                $p['la_brapci_rdf'] = 0;
                                $p['la_ano'] = $dados['ANO'];
                                if (isset($dados['DOI'])) {
                                    $p['la_doi'] = $dados['DOI'];
                                } else {
                                    $p['la_doi'] = '';
                                }
                                $p['la_pais'] = $dados['PAIS'];
                                $p['la_title'] = $dados['TITULO'];
                                $p['la_url'] = $dados['HOME-PAGE'];
                                $p['la_lang'] = $Lang->code($dados['IDIOMA']);
                                $p['la_natureza'] = $Natureza->natureza($dados['NATUREZA'], 'A');
                                $p['la_seq'] = $attr['SEQUENCIA-PRODUCAO'];

                                /***************************************** Info - adicionais */
                                if (isset($line['INFORMACOES-ADICIONAIS'])) {
                                    $inf = (array)$line['INFORMACOES-ADICIONAIS'];
                                    if (isset($inf['@attributes'])) {
                                        $inf = (array)$inf['@attributes'];
                                        if (isset($inf['DESCRICAO-INFORMACOES-ADICIONAIS'])) {
                                            $p['la_title'] .= ': ' . $inf['DESCRICAO-INFORMACOES-ADICIONAIS'];
                                        }
                                    }
                                }


                                /*********************************** Setores */
                                $p['la_setor'] = '';
                                if (isset($line['SETORES-DE-ATIVIDADE'])) {
                                    $deta = (array)$line['SETORES-DE-ATIVIDADE'];
                                    $deta = (array)$deta['@attributes'];
                                    foreach ($deta as $set) {
                                        if ($set != '') {
                                            $setor = $SetorAtividade->setor($set);
                                            if ($p['la_setor'] != '') {
                                                $p['la_setor'] .= ';';
                                            }
                                            $p['la_setor'] .= $setor;
                                        }
                                    }
                                } else {
                                    //pre($line);
                                }
                                /****************** AUTHORES */
                                $auth = (array)$line['AUTORES'];
                                $authn = '';
                                if (count($auth) == 1) {
                                    $autx = $auth;
                                    $auth = array();
                                    array_push($auth, $autx);
                                }

                                for ($ar = 0; $ar < count($auth); $ar++) {
                                    $aaa = (array)$auth[$ar];
                                    $authp = $aaa['@attributes'];
                                    if (strlen($authn) > 0) {
                                        $authn .= '; ';
                                    }
                                    $nome = (string)$authp['NOME-COMPLETO-DO-AUTOR'];
                                    $authn .= nbr_author($nome, 1);
                                }
                                $p['la_authors'] = $authn;
                                $p['la_author_total'] = count($auth);

                                $rst = $this->where('la_author', $id)
                                    ->where('la_title', $p['la_title'])
                                    ->where('la_ano', $p['la_ano'])
                                    ->findAll();

                                if (count($rst) == 0) {
                                    $idp = $this->set($p)->insert();
                                } else {
                                    $idp = $rst[0]['id_la'];
                                }

                                /****************** KEYWORDS */
                                if (isset($line['PALAVRAS-CHAVE'])) {
                                    $Keywords = new \App\Models\LattesExtrator\LattesKeywords();
                                    $dados = (array)$line['PALAVRAS-CHAVE'];
                                    $dados = (array)$dados['@attributes'];
                                    $Keywords->keyword_xml($idp, $dados, 'Z');
                                }
                            }
                        }
                    }
                }
            }
        }
        return 'ok';
    }
}
