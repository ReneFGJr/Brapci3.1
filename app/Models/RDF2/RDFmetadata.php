<?php

namespace App\Models\RDF2;

use CodeIgniter\Model;

class RDFmetadata extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'rdfmetadatas';
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

    function simpleMetadata($ID)
    {
        $RDF = new \App\Models\RDF2\RDF();
        $RDFconcept = new \App\Models\RDF2\RDFconcept();
        $RDFdata = new \App\Models\RDF2\RDFdata();

        $dt = $RDF->le($ID);
        $dd = [];

        $sm = [
            'hasTitle' => [],
            'hasCover' => [],
            'hasSectionOf' => [],
            'hasAuthor' => [],
        ];

        $da = $dt['data'];
        foreach ($da as $id => $line) {
            $lang = $line['Lang'];
            $prop = $line['Property'];

            if (isset($sm[$prop])) {
                if (!isset($dd[$prop][$lang])) {
                    $dd[$prop][$lang] = [];
                }
                $dc = [];
                $dc[$line['Caption']] = $line['ID'];
                array_push($dd[$prop][$lang], $dc);
            }
        }

        /*************** IDIOMA Preferencial */
        $lg = $this->langPref();
        $de = [];
        foreach ($sm as $prop => $line) {
            foreach ($lg as $id => $lang) {
                if (isset($dd[$prop][$lang])) {
                    if (!isset($de[$prop])) {
                        if (isset($dd[$prop][$lang][0])) {
                            $vlr = $dd[$prop][$lang][0];
                            $de[$prop] = trim(key($vlr));
                        }
                    }
                }
            }
        }

        $dr['ID'] = $ID;
        $dr['data'] = $de;
        return $dr;
    }

    function langPref()
    {
        $dt = ['pt', 'es', 'en', 'nn'];
        return $dt;
    }

    function metadata($ID)
    {
        $RDF = new \App\Models\RDF2\RDF();
        $RDFconcept = new \App\Models\RDF2\RDFconcept();
        $RDFdata = new \App\Models\RDF2\RDFdata();

        $dt = $RDF->le($ID);
        $dd = [];

        $class = $dt['concept']['c_class'];
        switch ($class) {
            case 'Issue':
                return $this->metadataIssue($dt);
                break;
            case 'Article':
                return $this->metadataWork($dt);
                break;
            case 'Book':
                return $this->metadataWork($dt);
                break;
            case 'Proceeding':
                return $this->metadataWork($dt);
                break;
            default:
                echo h($class);
                exit;
        }
    }

    function metadataIssue($dt, $simple = false)
    {
        $ID = $dt['concept']['id_cc'];
        $da = $dt['data'];
        $dr = [];
        $dr['jnl_name'] = '';
        $dr['ID'] = $ID;
        $w = [];
        $dr['Class'] = $dt['concept']['c_class'];
        foreach ($da as $id => $line) {
            $lang = $line['Lang'];
            $prop = $line['Property'];
            switch ($prop) {
                case 'hasPublicationIssueOf':
                    $dr['jnl_name'] = $line['Caption'];
                    $dr['jnl_rdf'] = $line['ID'];
                    break;
                case 'dateOfPublication':
                    $dr['is_year'] = $line['Caption'];
                    break;
                case 'hasVolumeNumber':
                    $dr['is_nr'] = $line['Caption'];
                    break;
                case 'hasVolume':
                    $dr['is_vol'] = $line['Caption'];
                    break;
                case 'hasIssueOf':
                    if (!$simple) {
                        if (!isset($dr['works'])) {
                            $dr['works'] = [];
                        }
                        array_push($w, $line['ID']);
                    }
                    break;
            }
        }
        if (isset($dr['jnl_rdf'])) {
            $Source = new \App\Models\Base\Sources();
            $dt = $Source->where('jnl_frbr', $dr['jnl_rdf'])->first();
            $dr['Publication'] = $dt['jnl_name'];
            $dr['PublicationAcronic'] = $dt['jnl_name_abrev'];
            $dr['PublicationUrl'] = $dt['jnl_url'];
        }
        if (!$simple) {
            $dr['works'] = [];
            foreach ($w as $id => $line) {
                $rsp = $this->simpleMetadata($line);
                if ($rsp['data'] == []) {
                    $rsp['data']['hasTitle'] = '::no title avaliable::';
                }
                array_push($dr['works'], $rsp);
            }
        }
        return $dr;
    }

    function metadataWork($dt, $simple = false)
    {
        $RDF = new \App\Models\RDF2\RDF();
        $ID = $dt['concept']['id_cc'];
        $da = $dt['data'];
        /************ DD*/
        $dd = [];

        foreach ($da as $id => $line) {
            $lang = $line['Lang'];
            $prop = $line['Property'];
            if (!isset($dd[$prop][$lang])) {
                $dd[$prop][$lang] = [];
            }
            $dc = [];
            $dc[$line['Caption']] = $line['ID'];
            array_push($dd[$prop][$lang], $dc);
        }

        $dr['ID'] = $ID;
        $dr['Class'] = $dt['concept']['c_class'];
        $dr['title'] = troca($this->simpleExtract($dd, 'hasTitle'), ["\n", "\r"], '');
        $dr['creator_author'] = [];
        $dr['Authors'] = '';
        if (isset($dd['hasOrganizator'])) {
            $dr['creator_author'] = $this->arrayExtract($dd, 'hasOrganizator', '(org)');
        } else {
            $dr['creator_author'] = $this->arrayExtract($dd, 'hasAuthor');
        }
        $dr['description'] = troca($this->simpleExtract($dd, 'hasAbstract'), ["\n", "\r"], '');
        $dr['subject'] = $this->arrayExtract($dd, 'hasSubject');

        $year = $this->simpleExtract($dd, 'wasPublicationInDate');
        if ($year != null) { $dr['year'] = $year; }

        /***************************** ISSUE */
        $ISSUE = $this->arrayExtract($dd, 'hasIssueOf');
        if (isset($ISSUE[0])) {
            $dtIssue = $RDF->le($ISSUE[0]['ID']);
            $simple = true;
            $dtIssue = $this->metadataIssue($dtIssue, $simple);
            $dr['Issue'] = $dtIssue;
            $dr['year'] = $dtIssue['is_year'];
            if (isset($dtIssue['Publication']))
                {
                    $dr['publisher'] = $dtIssue['Publication'];
                } else {
                    $dr['publisher'] = ':: Not informed Yet ::';
                }

        }

        $editora = $this->arrayExtract($dd, 'isPublisher');
        $place = $this->arrayExtract($dd, 'isPlaceOfPublication');
        $publisher = '';
        for ($r = 0; $r < count($editora); $r++) {
            $ln1 = $editora[$r];
            if (isset($place[$r])) {
                if ($publisher != '') {
                    $publisher .= '; ';
                }
                $ln2 = $place[$r];
                $publisher .= $ln2['name'] . ': ' . $ln1['name'];
            } else {
                if ($publisher != '') {
                    $publisher .= '; ';
                }
                $publisher .= $ln1['name'] . ': [s.n.]';
            }
        }
        $dr['publisher'] = $publisher;

        /************************************************************* COVER */
        $RDFimage = new \App\Models\RDF2\RDFimage();
        $dr['cover'] = $this->simpleExtract($dd, 'hasCover');

        /*************************************** SOURCE JOURNAL / PROCEEDING */
        if ($publisher == '') {
            $Source = new \App\Models\Base\Sources();
            $dj = $this->arrayExtract($dd, 'isPartOfSource');
            if (isset($dj[0])) {
                $dj = $Source->where('jnl_frbr', $dj[0]['ID'])->first();
                $dr['publisher'] = $this->simpleExtract($dd, 'isPartOfSource');
                $Cover = new \App\Models\Base\Cover();
                $dr['cover'] = $Cover->cover($dj['id_jnl']);
            }
        }

        /******************* ISBN */
        $ISBN = new \App\Models\ISBN\Index();
        $isbn = $this->arrayExtract($dd, 'hasISBN');
        $dr['isbn'] = '';
        foreach ($isbn as $value) {
            $visbn = $value['name'];
            $visbn = $ISBN->format($visbn);
            if ($dr['isbn'] != '') {
                $dr['isbn'] .= ' | ';
            }
            $dr['isbn'] .= $visbn;
        }


        /************************** Pages */
        $hasPage = $this->simpleExtract($dd, 'hasPage');
        if ($hasPage != '') {
            $dr['pages'] = $hasPage;
        }

        /************************** Resource */
        $dr['resource_pdf'] = PATH . '/download/' . $ID;


        /*********************** Section */
        switch ($dr['Class']) {
            case 'Book':
                $dr['section'][0] = ['name' => 'Book - Livro'];
                break;
            default:
                $dr['section'] = $this->arrayExtract($dd, 'hasSectionOf');
                if ($dr['section'] == []) {
                    $dr['section'][0] = ['name' => 'No Section'];
                }
                break;
        }
        if (!$simple) { $dr['data'] = $dd; }
        return $dr;
    }

    function arrayExtract($dt, $class, $suf = '')
    {
        $RSP = [];
        if (isset($dt[$class])) {
            $data = $dt[$class];
            foreach ($data as $lg) {
                foreach ($lg as $ida => $line) {
                    $name = [];
                    $name['name'] = trim(key($line));
                    $name['ID'] = $line[key($line)];
                    if ($suf != '') {
                        $name['complement'] = $suf;
                    }
                    array_push($RSP, $name);
                }
            }
        }
        return $RSP;
    }

    function simpleExtract($dt, $class)
    {
        $lang = $this->langPref();
        if (isset($dt[$class])) {
            foreach ($dt as $nn => $line) {
                if ($nn == $class) {
                    foreach ($lang as $lg) {
                        if (isset($line[$lg])) {
                            $rsp = key($line[$lg][0]);
                            return ($rsp);
                        }
                    }
                }
            }
        }
    }
}
