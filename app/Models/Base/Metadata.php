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


    function metadata($meta, $erros = false)
    {
        $RDF = new \App\Models\Rdf\RDF();
        $COVER = new \App\Models\Base\Cover();
        $ISBN = new \App\Models\ISBN\Index();
        $Source = new \App\Models\Base\Sources();

        if (isset($meta['data']) and (count($meta['data']) == 0)) {
            $RDF->exclude($meta['concept']['id_cc']);
            return [];
        }

        $issue_proceessed = array();
        $this->metadata = array();
        if (isset($meta['concept'])) {
            $concept = $meta['concept'];
            $this->lets('Class', trim($concept['c_class']));
            $this->lets('ID', trim($concept['id_cc']));
            $m = '';

            foreach ($concept as $class => $value) {
                switch ($class) {
                    case 'c_class':
                        $this->lets('Type', $value);
                        break;
                    case 'id_cc':
                        $this->lets('Article_id', $value);
                        break;
                    case 'n_name':
                        $this->lets('Identifier', $value);
                        break;
                    default:
                        break;
                }
            }
        }

        /************************************************************** PROPERTIES **/
        if (isset($meta['data'])) {
            $data = $meta['data'];
            for ($r = 0; $r < count($data); $r++) {
                $line = $data[$r];
                $class = trim($line['c_class']);
                $value = $line['n_name2'];
                $lang = $line['n_lang2'];
                $valueO = $line['n_name'];
                $langO = $line['n_lang'];
                $ddv1 = $line['d_r1'];
                $ddv2 = $line['d_r2'];

                if (($class == 'hasRegisterId') and (substr($valueO, 0, 3) == '10.')) {
                    $class = 'hasDOI';
                    $line['n_name2'] = $valueO;
                }

                switch ($class) {
                    case 'hasBookChapter':
                        $type = $this->metadata['Class'];
                        if ($type == 'Book') {
                            $db = $RDF->le($line['d_r1']);
                            //$this->metadata['book'] = $RDF->c($line['d_r1']);
                        } elseif ($type == 'BookChapter') {
                            $this->metadata['bookID'] = $line['d_r1'];
                            $this->lets('books', $RDF->c($line['d_r1']));
                        }

                        $link = '<a href="' . PATH . COLLECTION . '/v/' . $line['d_r2'] . '" class="summary_a">';
                        $linka = '</a>';

                        $bn = $RDF->directory($line['d_r2']);

                        if (file_exists($bn . 'name.nm')) {
                            $value = '<p class="summary_ln">' . $link . $RDF->c($line['d_r2']) . $linka . '</p>';
                        } else {
                            $value = 'Aguarde, em processamento<br><br>';
                        }
                        $this->lets('summary', $value);
                        break;
                    case 'hasClassificationAncib':
                        $this->let('CatAncib', $value);
                        $this->let_array('CatAncibArray', $ddv2, $value);
                        break;
                    case 'hasFileStorage':
                        $this->let('PDFfile', $value);
                        $this->let('PDF_id', $ddv1);
                        break;
                    case 'hasUrl':
                        $url = trim($line['n_name']);
                        $url = '<a href="' . $url . '" target="_new" class="p-1">' . bsicone('url') . '</a>';
                        $this->lets('links', $url);
                        break;
                    case 'hasISBN':
                        $value = $ISBN->format($valueO);
                        $this->let('isbn', $value);
                        break;
                    case 'hasDOI':
                        $doi = trim($line['n_name2']);
                        if (substr($doi, 0, 1) == '1') {
                            $doi = "https://doi.org/" . $doi;
                            $this->lets('DOI', $doi);
                            $doi = '<a class="summary_a" href="' . $doi . '" target="_blank">' . $doi . '</a>';
                            $this->lets('lDOI', $doi);
                        } else {
                            $this->lets('lDOI', $doi);
                            $this->lets('DOI', $doi);
                        }
                        break;
                    case 'Identifier.DOI':
                        $this->lets('DOI', $value . '##xml:lang=' . $lang);
                        break;
                    case 'hasAbstract':
                        $this->leta('Abstract', $valueO, $langO);
                        break;
                    case 'hasLicense':
                        $value = '<img src="' . URL . '/img/icons/cc/' . $value . '.png" style="max-height: 45px;">';
                        $this->lets('license', $value);
                        break;
                    case 'hasAuthor':
                        $value = nbr_author($value, 7);
                        $this->leta('Authors', $value, $ddv2);
                        break;
                    case 'hasOrganizator':
                        $value = nbr_author($value, 7);
                        $this->leta('Organizator', $value, $ddv2);
                        break;
                    case 'dateOfPublication':
                        $this->lets('year', $value);
                        break;
                    case 'isPublisher':
                        $this->leta('Editora', $value, $ddv2);
                        break;
                    case 'hasSectionOf':
                        $this->leta('Sections', $value, $ddv2);
                        break;
                    case 'hasCover':
                        $cover = $COVER->image($ddv2);
                        $this->lets('cover', $cover);
                        break;
                    case 'hasPage':
                        $this->lets('Pages', $value);
                        break;
                    case 'isPlaceOfPublication':
                        $this->leta('EditoraLocal', $value, $ddv2);
                        break;
                    case 'isPubishIn':
                        $this->lets('Journal', $value);
                        $this->lets('jnl_frbr', $ddv2);
                        $ds = $Source->where('jnl_frbr', $ddv2)->first();
                        if ($ds != '') {
                            $this->metadata['id_jnl'] = $ds['id_jnl'];
                        }
                        break;
                    case 'hasLanguageExpression':
                        $this->lets('Expression', $value, $valueO);
                        break;
                    case 'hasTitle':
                        $valueO = trim(strip_tags($valueO));
                        $this->lets('title', $valueO);
                        $this->let('Idioma', $langO);
                        $this->leta('Title', $valueO, $langO);
                        break;
                    case 'hasSubject':
                        if (!isset($this->metadata['Keywords'][$lang][$value])) {
                            $this->metadata['Keywords'][$lang][$value] = $ddv2;
                        }
                        break;
                    case 'hasPublicationNumber':
                        $this->metadata['Issue']['nr'] = $value;
                        break;
                    case 'hasPublicationVolume':
                        $this->metadata['Issue']['vol'] = $value;
                        break;
                    case 'prefLabel':
                        $this->lets('prefLabel', $valueO);
                        break;
                    case 'altLabel':
                        $this->metadata['Issue']['leg'] = $valueO;
                        break;
                    case 'hiddenLabel':
                        $this->let('hiddenLabel', $value . $valueO);
                        break;
                    case 'hasPageStart':
                        $this->lets('PAGi', $value);
                        break;
                    case 'hasPageEnd':
                        $this->lets('PAGf', $value);
                        break;
                    case 'hasPlace':
                        $this->lets('Place', $value);
                        break;
                    case 'hasISSN':
                        $this->let('ISSN', $valueO);
                        break;
                    case 'hasEmail':
                        $this->let('email', $valueO);
                        break;
                    case 'hasCollection':
                        $this->let('Collections', $value);
                        break;
                    case 'hasIssue':
                        $this->let('Issue', $ddv1);
                        break;
                    case 'hasIdRegister':
                        break;
                    case 'hasEditor':
                        $this->let('Editor', anchor(PATH . '/autoridade/v/' . $ddv2, $value));
                        break;
                    case 'affiliatedWith':
                        $this->let('Affiliation', $ddv2, $value);
                        $this->let_array('AffiliationR', $value, $ddv1);
                        break;
                    case 'hasAffiliation':
                        $this->let('Affiliation', $ddv2, $value);
                        $this->let_array('AffiliationR', $value, $ddv1);
                        break;
                    case 'hasGender':
                        $this->lets('Gender', $value);
                        break;
                    case 'acronym':
                        $this->lets('Sigla', $value);
                        break;
                    case 'hasPicture':
                        $this->let_array('Imagem', $valueO, $ddv1);
                        break;
                    case 'fullText':
                        $this->let_array('Fulltext', $valueO, $ddv1);
                        break;
                    case 'hasGoogleSchollarId':
                        $this->let_array('Google', $valueO, $ddv1);
                        break;
                    default:
                        if ($erros == true) {
                            echo '=Not identify class=>' . $class . ' == []' . $value . '(' . $ddv1 . ') [O]' . $valueO . '(' . $ddv2 . ')<br>';
                        }
                        break;
                }
            }
        }


        return $this->metadata;
    }


}
