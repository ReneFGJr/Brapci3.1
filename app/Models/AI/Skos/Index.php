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
                $sx .= metarefresh(PATH . COLLECTION . '/skos/viewid/' . $this->id);
                break;
            case 'export':
                $sx = $this->export($this->id);
                //$sx .= metarefresh(PATH . COLLECTION . '/skos/viewid/' . $this->id);
                break;
            case 'export_all':
                if ($id == '') { $id = 1; }
                $sx = $this->export_all_skos($id);
                break;
            case 'viewid':
                $sx .= $this->viewid($this->id);
                break;
            case 'edit':
                $sx .= form($this);
                break;
            default:
                $sx .= anchor(PATH . COLLECTION . '/skos/export_all/1', msg('export_all'));
                $sx .= h(lang('brapci.ai.skos'));
                $sx .= tableview($this);
                break;
        }
        $sx = bs($sx);
        return $sx;
    }

    function export_all_skos($id)
        {
            $dt = $this->where('id_sk >=', $id)->First();
            if ($dt != '')
                {
                    $sx = $this->export($dt['id_sk']);
                    $id = $dt['id_sk'] + 1;
                    $sx .= h($dt['id_sk'].' == '.$id);

                    $sx .= metarefresh(PATH . COLLECTION . '/skos/export_all/' .($id),2);
                } else {
                    $sx = h('Exportação finalizada');
                }
            return $sx;
        }

    function viewid($id)
    {
        $dt = $this->find($id);
        $sx = '<h1>' . $dt['sk_name'] . '</h1>';

        $sx .= bsc($this->btn_inport($id) . ' | '.$this->btn_export($id),12);

        $sx .= $this->show_terms($id);
        return $sx;
    }

    function load_terms()
        {
        $VCterms = new \App\Models\AI\Skos\VCterms();
        $dt = $VCterms->findAll();
        $terms = array();
        for ($r=0;$r < count($dt);$r++)
            {
                $line = $dt[$r];
                $terms[$line['id_vc']] = $line['vc_prefLabel'];
            }
        return $terms;
        }

    function show_terms($id)
        {
            $sx = '';
            $terms = $this->load_terms();
            $dt = $this
                ->join('brapci_chatbot.vc_link', 'id_sk = lk_skos','left')
                ->where('id_sk',$id)
                ->findAll();

            for($r=0;$r < count($dt);$r++)
                {
                    $line = $dt[$r];
                    $term = '';
                    for ($n=0;$n <= 15;$n++)
                        {
                            $fld = 'lk_word_' . $n;
                            $w0 = $line[$fld];
                            if (isset($terms[$w0]))
                                {
                                    $term .= $terms[$w0] . ' ';
                                }

                        }

                    $sx .= '<li>'.$term.'</li>';
                }
            return $sx;
        }

    function btn_inport($id)
    {
        $sx = '<a href="' . PATH . COLLECTION . '/skos/import/' . $id . '" class="btn btn-outline-primary">';
        $sx .= lang('brapci.import');
        $sx .= '</a>';
        return $sx;
    }

    function btn_export($id)
    {
        $sx = '<a href="' . PATH . COLLECTION . '/skos/export/' . $id . '" class="btn btn-outline-primary">';
        $sx .= lang('brapci.export');
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

    function export($id)
    {
        $sx = '';
        $VCconcepts = new \App\Models\AI\Skos\VCconcepts();

        /******************************* Exportar */
        $sx .= $VCconcepts->export_all($id);
        return $sx;
    }

    function import_thesa($id_th)
    {
        $VCterms = new \App\Models\AI\Skos\VCterms();
        $VCconcepts = new \App\Models\AI\Skos\VCconcepts();

        $dt = $this->find($id_th);
        $url = $dt['sk_uri'];

        $url = troca($url, '/terms/', '/terms_from_to/') . '/skos';
        $xml = read_link($url, 'CURL');
        $sx = h($url);
        $xml = troca($xml, 'rdf:', '');
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
                    $attr = (array)$line['@attributes'];
                    $uri = $attr['about'];
                    /**************************** PrefLabel */
                    if (isset($line['prefLabel'])) {
                        $PrefLabel = (array)$line['prefLabel'];
                        for ($r = 0; $r < count($PrefLabel); $r++) {
                            $term = troca(ascii(mb_strtolower($PrefLabel[$r])),' ','_');
                            $VCconcepts->term($term,$id_th,$uri);
                            $VCterms->terms_skos($PrefLabel[$r], $id_th);
                        }
                    }
                    /**************************** Related */
                    if (isset($line['altLabel'])) {
                        $altLabel = (array)$line['altLabel'];
                        for ($r = 0; $r < count($altLabel); $r++) {
                            $term = troca(ascii(mb_strtolower($altLabel[$r])), ' ', '_');
                            $VCconcepts->term($term, $id_th, $uri);
                            $VCterms->terms_skos($altLabel[$r], $id_th);
                        }
                    }

                    /**************************** Hidden */
                    if (isset($line['hiddenLabel'])) {
                        $hiddenLabel = (array)$line['hiddenLabel'];
                        for ($r = 0; $r < count($hiddenLabel); $r++) {
                            $term = troca(ascii(mb_strtolower($hiddenLabel[$r])), ' ', '_');
                            $VCconcepts->term($term, $id_th, $uri);
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
