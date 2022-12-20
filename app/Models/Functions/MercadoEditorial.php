<?php

namespace App\Models\Functions;

use CodeIgniter\Model;

class MercadoEditorial extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'mercadoeditorials';
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

    //var $endpoint = 'https://api.mercadoeditorial.org/api/v1.2/book?isbn=$ISBN';     /* Production */
    var $endpoint = 'https://sandbox.mercadoeditorial.org/api/v1.2/book?isbn=$ISBN'; /* SandBox */
    // https://api.mercadoeditorial.org/documentacao/v1.2

    function _call($isbn)
    {
        $apiKey = getenv('api_key_mercadoeditorial');
        $ISBN = new \App\Models\Functions\ISBN();
        $isbn = $ISBN->format($isbn);
        $isbn10 = $ISBN->isbn13to10($isbn);

        $url = $this->endpoint;
        $url = troca($url, '$ISBN', $isbn);

        $header = array();
        $header[] = 'Content-length: 0';
        $header[] = 'Content-type: application/json';
        $header[] = 'Authorization: ' . $apiKey;

        $ch = curl_init();
        //curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_URL, $url);
        //curl_setopt($ch, CURLOPT_HEADER, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        //curl_setopt($ch, CURLOPT_NOBODY, TRUE); // remove body
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        $head = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $erro = curl_errno($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);

        if (!$erro) {
            $dt = (array)json_decode($head);
            pre($dt);
            $dts = (array)$dt['status'];
            if ($dts['code'] == '101') {
                return array();
            }

            if ($dts['code'] == '200') {
                $dt = $this->prepare($dt);
            }

            return $dt;
        } else {
            echo 'Curl error: ' . curl_error($ch);
            return array();
        }
    }
    function prepare($dt)
    {
        $Lang = new \App\Models\Functions\Language();
        $dc = array();
        $dt = (array)$dt['books'][0];

        if (isset($dt['titulo'])) {
            $dc['title'] = trim($dt['titulo']) . ' ' . trim($dt['subtitulo']);
        } else {
            $dc['title'] = '';
        }

        /****************************** Authors */
        $dta = $dt['contribuicao'];
        $dc['authors'] = array();
        for ($r = 0; $r < count($dta); $r++) {
            $line = (array)$dta[$r];
            $nome = trim($line['nome']) . ' ' . trim($line['sobrenome']);
            $type = $line['tipo_de_contribuicao'];
            switch ($type) {
                case 'Autor':
                    array_push($dc['authors'], $nome);
                    break;
                default:
                    echo "OPS - Tipo autor: " . $type;
                    exit;
            }
        }

        /****************************** Date_published */
        if (isset($dt['ano_edicao'])) {
            $dc['published'] = trim($dt['ano_edicao']);
        } else {
            $dc['published'] = '';
        }

        /****************************** Pages */
        if (isset($dt['medidas'])) {
            $cat = (array)$dt['medidas'];
            if (isset($cat['paginas'])) {
                $dc['pages'] = trim($cat['paginas']);
            } else {
                $dc['pages'] = '';
            }
        } else {
            $dc['pages'] = '';
        }

        /****************************** Editora */
        if (isset($dt['editora'])) {
            $cat = (array)$dt['editora'];
            if (isset($cat['nome_fantasia'])) {
                $dc['editora'] = trim($cat['nome_fantasia']);
            } else {
                $dc['editora'] = '';
            }
        } else {
            $dc['pages'] = '';
        }

        /****************************** Cover */
        if (isset($dt['imagens'])) {
            $cat = (array)$dt['imagens'];
            if (isset($cat['imagem_primeira_capa'])) {
                $img = (array)$cat['imagem_primeira_capa'];
                $dc['cover'] = trim($img['media']);
            }
        } else {
            $dc['cover'] = '';
        }

        /****************************** Idioma */
        if (isset($dt['idioma'])) {
            $lang = $Lang->check(trim($dt['idioma']));
            $dc['lang'] = $lang;
        } else {
            $dc['lang'] = 'pt-BR';
        }

        /****************************** subjects */
        if (isset($dt['catalogacao'])) {
            $cat = (array)$dt['catalogacao'];
            if (isset($cat['palavras_chave'])) {
                $key = $cat['palavras_chave'];
                $key = troca($key, ',', ';');
                $key = troca($key, '; ', ';');
                $key = troca($key, ' ;', ';');
                $key = troca($key, ';;', ';');
                $key = explode(';', $key);
                $dc['subjects'] = $key;
            } else {
                $dc['subjects'] = array();
            }
        }

        /****************************** dewey_decimal */
        if (isset($dt['catalogacao'])) {
            $cat = (array)$dt['catalogacao'];
            if (isset($cat['cdd'])) {
                $dc['cdd'] = $cat['cdd'];
            }
        } else {
            $dc['cdd'] = '';
        }
        $dc['cdu'] = '';

        /****************************** overview */
        if (isset($dt['sinopse'])) {
            $dc['abstract'] = $dt['sinopse'];
        } else {
            $dc['abstract'] = '';
        }

        return $dc;
    }
}