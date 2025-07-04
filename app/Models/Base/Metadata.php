<?php

namespace App\Models\Base;

use CodeIgniter\Model;

class Metadata extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'metadatas';
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

    var $metadata = array();
    var $title = "";
    var $lang = '';

    function let($class, $value)
    {
        if (is_array($value)) {
            $this->metadata[$class] = $value;
        } else {
            $this->metadata[$class][] = trim($value);
        }
        return true;
    }

    function leta($class, $value, $array)
    {
        if (!isset($this->metadata[$class])) {
            $this->metadata[$class] = array();
        }
        if (!isset($this->metadata[$class][$array])) {
            $this->metadata[$class][$array] = '';
        }
        $vlr = $this->metadata[$class][$array];
        $this->metadata[$class][$array] .= trim($value);
        return true;
    }

    function let_array($class, $value, $array)
    {
        if (!isset($this->metadata[$class])) {
            $this->metadata[$class] = array();
        }
        if (!isset($this->metadata[$class][$array])) {
            $this->metadata[$class][$array] = '';
        }
        $vlr = $this->metadata[$class][$array];
        $this->metadata[$class][$array] = trim($value);
        return true;
    }


    function lets($class, $value)
    {
        if (!isset($this->metadata[$class])) {
            $this->metadata[$class] = '';
        }

        if ($this->metadata[$class] != '') {
            $this->metadata[$class] .= ' ';
        }
        $this->metadata[$class] .= trim($value);
        return true;
    }


    function metadata($meta, $reg)
    {
        $M = [];
        $RDF = new \App\Models\RDF2\RDF();
        $RDFData = new \App\Models\RDF2\RDFdata();
        $BaseCover = new \App\Models\Base\Cover();
        $ISBN = new \App\Models\ISBN\Index();
        $Source = new \App\Models\Base\Sources();
        $Issue = new \App\Models\Base\Issues();

        if (isset($meta['data']) and (count($meta['data']) == 0)) {
            //$RDF->exclude($meta['concept']['id_cc']);
            return [];
        }

        $idcc = $meta['concept']['id_cc'];
        $class = $meta['concept']['c_class'];

        $this->metadata = array();
        if (isset($meta['concept'])) {
            $concept = $meta['concept'];
            $MC = [
                'ID' => 'id_cc',
                'Identifier' => 'n_name',
                'Class' => 'c_class'
            ];

            foreach ($MC as $fld1 => $fld2) {
                if (isset($concept[$fld2])) {
                    $M[$fld1] = $concept[$fld2];
                }
            }
        }

        /************************************************************** PROPERTIES **/
        $authF = [];
        $keyWD = '';
        $keyWDen = '';
        $keyWDes = '';
        $abstC = '';
        $sectN = '';
        $PUBLI = '';

        /******************************** Fields */
        //echo $prop . '<br>';
        $p = [
            'hasOrganizator' => 'Organizer',
            'hasAuthor' => 'Authors',
            'hasSubject' => 'Subject',
            'hasAbstract' => 'Abstract',
            'hasCover' => 'Cover',
            'isPublisher' => 'Editor',
            'wasPublicationInDate' => 'Year',
            'hasISBN' => 'ISBN',
            'hasTitle' => 'Title',
            'hasDOI'=> 'DOI',
            'hasClassificationCDD' => 'CDD',
            'hasClassificationCDU' => 'CDU',
            'isPlaceOfPublication' => 'Place',
            'hasFileStorage' => 'File',
            'hasSectionOf'=> 'Section',
            'isPartOfSource','Journal'
        ];

        if (isset($meta['data'])) {
            $data = $meta['data'];
            foreach ($data as $idl => $line) {

                if (isset($line['Class'])) {
                    $class = trim($line['Class']);
                    $value = $line['Caption'];
                    $lang = $line['Lang'];
                    $prop = $line['Property'];
                    $ID = $line['ID'];

                    foreach ($p as $prp => $cls) {

                        if ($prop == $prp) {
                            if (!isset($M[$cls])) {
                                $M[$cls] = [];
                            }

                            ##################################### PUBLICATION
                            if ($cls == 'Journal') {
                                /* Veja no proximo linha 256 para artigos*/
                                $PUBLI = trim($line['Caption']);
                            }

                            ##################################### SECTION
                            if ($cls == 'Section') {
                                if ($sectN != '') {
                                    $sectN .= '; ';
                                }
                                $sectN .= trim($line['Caption']);
                            }

                            ##################################### KEYWORDS
                            if ($cls == 'Subject')
                                {
                                    switch($line['Lang']) {
                                        case 'en':
                                            if ($keyWDen != '') { $keyWDen .= '; '; }
                                            $keyWDen .= trim($line['Caption']);
                                            break;
                                        case 'pt':
                                            if ($keyWD != '') { $keyWD .= '; '; }
                                            $keyWD .= trim($line['Caption']);
                                            break;
                                        case 'es':
                                            if ($keyWDes != '') { $keyWDes .= '; '; }
                                            $keyWDes .= trim($line['Caption']);
                                            break;
                                    }
                                }
                            ##################################### abstract
                            if ($cls == 'Abstract') {
                                if ($abstC != '') {
                                    $abstC .= chr(13);
                                }
                                $txt = troca(trim($line['Caption']), chr(13), ' ');
                                $txt = troca($txt,chr(10),' ');
                                $txt = trim($txt);
                                $abstC .= $txt;
                            }
                            #array_push($M[$cls], ['ID'=>$ID,'value' => $value, 'lang' => $lang]);
                            if (($cls == 'Organizer') or ($cls == 'Authors'))
                                {
                                    $lang = 'nn';
                                    $auth2 = [];
                                    $auth2['name'] = $line['Caption'];
                                    $auth2['ID'] = $line['ID'];
                                    array_push($authF,$auth2);
                                }

                            if ($lang != 'nn') {
                                if (!isset($M[$cls][$lang])) {
                                    $M[$cls][$lang] = [];
                                }
                                array_push($M[$cls][$lang], $value);
                            } else {
                                array_push($M[$cls], $value);
                            }
                        }
                    }

                    /********************************** Issue */
                    $ISU = $Issue->getIssue4Work($M['ID'], $meta, $reg);
                    $M['authors'] = $authF;
                    $M['Issue'] = $ISU;
                    if (isset($ISU['year'])) {
                        $M['YEAR'] = $ISU['year'];
                        $M['ISSUE'] = $ISU['issue'];
                    }
                }
            }
            /********************************** Cover */
            switch ($M['Class']) {
                case 'BookChapter':
                    $M['COVER'] = $BaseCover->bookChapter($M['ID']);
                    $M['COLLECTION'] = 'BC';
                    break;
                case 'Book':
                    $M['COVER'] = $BaseCover->book($M['ID']);
                    $M['COLLECTION'] = 'BK';
                    break;
                case 'Article':
                    if (isset($M['Issue']['id_jnl'])) {
                        $jnl = $M['Issue']['id_jnl'];
                    } else {
                        $jnl = 99999;
                    }
                    /******************************* Collection */
                    $SRC = $Source->where('id_jnl', $jnl)->first();
                    $PUBLI = $SRC['jnl_name'];
                    $M['COLLECTION'] = $SRC['jnl_collection'];
                    $M['COVER'] = $BaseCover->cover($jnl);
                    break;
                case 'Proceeding':
                    if (isset($M['Issue']['id_jnl'])) {
                        $jnl = $M['Issue']['id_jnl'];
                    } else {
                        $jnl = 99999;
                    }
                    $M['COVER'] = $BaseCover->cover($jnl);
                    $M['COLLECTION'] = 'EV';
                    break;
            }

            $M['KEYWORDS'] = $keyWD;
            $M['KEYWORDS_EN'] = $keyWDen;
            $M['KEYWORDS_ES'] = $keyWDes;
            $M['KEYWORDS_FR'] = '';

            $M['ABSTRACTS'] = $abstC;

            /* Ordena Session */
            $sectN = troca($sectN,'; ',';');
            $sectA = explode(';', $sectN);
            sort($sectA);
            $sectN = implode('; ',$sectA);

            $M['SESSION'] = $sectN;
            $M['PUBLICATION'] = $PUBLI;

            $this->metadata = $M;
            return $this->metadata;
        }
    }
}
