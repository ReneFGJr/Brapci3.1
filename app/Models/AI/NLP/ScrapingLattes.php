<?php

namespace App\Models\AI\NLP;

use CodeIgniter\Model;

class ScrapingLattes extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'scrapinglattes';
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

    function search()
    {
        $url = 'https://buscatextual.cnpq.br/buscatextual/busca.do?metodo=forwardPaginaResultados&registros=0;10';
        $url .= '&query=%28%2Bidx_assunto%3A%28%22ciencia+aberta%22%29++%2Bidx_nacionalidade%3Ae%29+or+%28%2Bidx_assunto%3A%28%22dados+de+pesquisa%22%29++%2Bidx_nacionalidade%3Ab+%5E500+%29';
        $url .= '&analise=cv';
        $url .= '&tipoOrdenacao=null';
        $url .= '&paginaOrigem=index.do';
        $url .= '&mostrarScore=true';
        $url .= '&mostrarBandeira=true';
        $url .= '&modoIndAdhoc=null';
        $txt = htmlentities($url);
        $txt = urldecode($txt);

        $txt = read_link($url);
        echo $txt;
        return $url;
    }
}
