<?php

namespace App\Models\AI\Skos;

use CodeIgniter\Model;

class Index extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'brapci_chatbot.skos';
    protected $primaryKey       = 'id_sk';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_sk', 'sk_name', 'sk_uri',
        'sk_description', 'updated_at', 'deleted_at',
    ];
    protected $typeFields    = [
        'hidden', 'string', 'string*',
        'text', 'now', 'hidden',
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

    function index($act, $id)
    {
        $sx = '';
        $this->id = $id;
        $this->path = PATH . COLLECTION . '/skos';
        $this->path_back = PATH . COLLECTION . '/skos';

        switch ($act) {
            case 'import':
                $sx = $this->import($this->id);
                $sx .= metarefresh(PATH . COLLECTION . '/skos/' . $this->id);
                break;
            case 'viewid':
                $sx = $this->viewid($this->id);
                break;
            case 'edit':
                $sx .= form($this);
                break;
            default:
                $sx .= h(lang('brapci.ai.skos'));
                $sx .= tableview($this);
                break;
        }
        $sx = bs($sx);
        return $sx;
    }

    function viewid($id)
    {
        $dt = $this->find($id);
        $sx = '<h1>' . $dt['sk_name'] . '</h1>';
        $sx .= $this->btn_inport($id);
        return $sx;
    }

    function btn_inport($id)
    {
        $sx = '<a href="' . PATH . COLLECTION . '/skos/import/' . $id . '" class="btn btn-outline-primary">';
        $sx .= lang('brapci.import');
        $sx .= '</a>';
        return $sx;
    }

    function import($id)
    {
        $sx = '';
        $dt = $this->find($id);
        $url = $dt['sk_uri'];
        $txt = read_link($url, 'CURL');

        /*************** SKOSMOS */
        /* <body class="vocab-thesaurus"> */

        /*************** THESA */
        /* <title>Thesa */
        if (strpos($txt, '<title>Thesa') > 0) {
            $sx .= $this->import_thesa($id);
        }
        return $sx;
    }
    function import_thesa($id_th)
    {
        $VCterms = new \App\Models\AI\Skos\VCterms();

        $dt = $this->find($id_th);
        $url = $dt['sk_uri'];

        $url = troca($url, '/terms/', '/terms_from_to/') . '/skos';
        $xml = read_link($url, 'CURL');
        $sx = h($url);
        $xml = simplexml_load_string($xml);
        $xml = (array)$xml;

        if (isset($xml['Collection'])) {
            $Collection = (array)$xml['Collection'];
            $dd['sk_name'] = $Collection['name'];
            $dd['updated_at'] = date("Y-m-d H:i:s");
            //$dd['sk_description'] = (array)$Collection['description'];
            $dd['sk_description'] = '';
            $this->set($dd)->where('id_sk', $id_th)->update();

            /*************************** Importar termos */
            $Concept = (array)$xml['Concept'];
            for ($ct = 0; $ct < count($Concept); $ct++) {
                if (isset($Concept[$ct])) {
                    $line = (array)$Concept[$ct];

                    /**************************** PrefLabel */
                    if (isset($line['prefLabel'])) {
                        $PrefLabel = (array)$line['prefLabel'];
                        for ($r = 0; $r < count($PrefLabel); $r++) {
                            $VCterms->terms_skos($PrefLabel[$r], $id_th);
                        }
                    }
                    /**************************** Related */
                    if (isset($line['altLabel'])) {
                        $altLabel = (array)$line['altLabel'];
                        for ($r = 0; $r < count($altLabel); $r++) {
                            $VCterms->terms_skos($altLabel[$r], $id_th);
                        }
                    }

                    /**************************** Hidden */
                    if (isset($line['hiddenLabel'])) {
                        $hiddenLabel = (array)$line['hiddenLabel'];
                        for ($r = 0; $r < count($hiddenLabel); $r++) {
                            $VCterms->terms_skos($hiddenLabel[$r], $id_th);
                        }
                    }
                }
            }
        }

        return $sx;
    }


    function edit($id)
    {
    }
}
