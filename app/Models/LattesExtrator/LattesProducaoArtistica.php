<?php

namespace App\Models\LattesExtrator;

use CodeIgniter\Model;

class LattesProducaoArtistica extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'lattesproducaoartisticas';
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

    function zerezima_dados_xml($id)
    {
        $this->where('la_author', $id)->delete();
        return true;
    }

    function producao_xml($id)
    {
        $Lang = new \App\Models\Language\Lang();
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
                $art = (array)$art['OUTRA-PRODUCAO-ARTISTICA-CULTURAL'];

                foreach ($art as $id => $line) {


                    $line = (array)$line;
                    $attr = $line['@attributes'];

                    $dados = (array)$line['DADOS-BASICOS-DE-OUTRA-PRODUCAO-ARTISTICA-CULTURAL'];
                    $dados = (array)$dados['@attributes'];

                    pre($dados,false);
                    $p = array();
                    $p['lp_author'] = $id;
                    $p['lp_brapci_rdf'] = 0;
                    $p['lp_ano'] = $dados['ANO'];
                    if (isset($dados['DOI'])) {
                        $p['lp_doi'] = $dados['DOI'];
                    } else {
                        $p['lp_doi'] = '';
                    }
                    $p['lp_title'] = $dados['TITULO'];
                    $p['lp_url'] = $dados['HOME-PAGE'];
                    $p['lp_lang'] = $Lang->code($dados['IDIOMA']);
                    $p['lp_natureza'] = substr($dados['NATUREZA'], 0, 1);
                    $p['lp_seq'] = $attr['SEQUENCIA-PRODUCAO'];

                    pre($p);

                    $deta = (array)$line['DETALHAMENTO-DO-ARTIGO'];
                    $deta = (array)$deta['@attributes'];

                    $p['lp_journal'] = $deta['TITULO-DO-PERIODICO-OU-REVISTA'];
                    $p['lp_issn'] = $deta['ISSN'];
                    $vl = trim($deta['VOLUME']);
                    $nr = trim($deta['FASCICULO']);
                    if (
                        $vl != ''
                    ) {
                        $vl = 'v. ' . $vl;
                    }
                    if (
                        $nr != ''
                    ) {
                        $nr = 'n. ' . $nr;
                    }
                    $p['lp_place'] = $deta['LOCAL-DE-PUBLICACAO'];
                    $p['lp_vol'] = $vl;
                    $p['lp_nr'] = $nr;

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
                    $p['lp_authors'] = $authn;
                    $p['lp_author_total'] = count($auth);

                    $rst = $this->where('lp_author', $id)
                        ->where('lp_title', $p['lp_title'])
                        ->where('lp_ano', $p['lp_ano'])
                        ->where('lp_journal', $p['lp_journal'])
                        ->findAll();

                    if (count($rst) == 0) {
                        $idp = $this->set($p)->insert();
                    } else {
                        $idp = $rst[0]['id_lp'];
                    }

                    /****************** KEYWORDS */
                    if (isset($line['PALAVRAS-CHAVE'])) {
                        $Keywords = new \App\Models\LattesExtrator\LattesKeywords();
                        $dados = (array)$line['PALAVRAS-CHAVE'];
                        $dados = (array)$dados['@attributes'];
                        $Keywords->keyword_xml($idp, $dados, 'A');
                    }
                }
            }
        }
        return 'ok';
    }
}
