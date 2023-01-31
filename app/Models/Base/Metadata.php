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
        $this->metadata[$class][] = $value;
        return true;
    }

    function lets($class, $value)
    {
        if (!isset($this->metadata[$class]))
            {
            $this->metadata[$class] = '';
            }
            //pre($value,false);
        $this->metadata[$class] .= $value . ' ';
        return true;
    }

    function metadata($meta)
    {
        $RDF = new \App\Models\Rdf\RDF();
        $COVER = new \App\Models\Base\Cover();
        $ISBN = new \App\Models\ISBN\Index();
        $this->metadata = array();
        if (isset($meta['concept'])) {
            $concept = $meta['concept'];
            $this->lets('class', trim($concept['c_class']));
            $m = '';

            foreach ($concept as $class => $value) {
                switch ($class) {
                    case 'c_class':
                        $this->lets('type', $value);
                        break;
                    case 'id_cc':
                        $this->lets('article_id', $value);
                        break;
                    case 'n_name':
                        $this->lets('Identifier', $value);
                        break;
                    default:
                        break;
                }
            }
        }

        /*************************************************************************/
        if (isset($meta['data'])) {
            $data = $meta['data'];

            for ($r = 0; $r < count($data); $r++) {
                $line = $data[$r];
                $class = $line['c_class'];
                $value = $line['n_name2'];
                $lang = $line['n_lang2'];
                $valueO = $line['n_name'];
                $langO = $line['n_lang'];
                $ddv1 = $line['d_r1'];
                $ddv2 = $line['d_r2'];

                switch ($class) {
                    case 'hasBookChapter':
                        if (isset($this->metadata['book']))
                            {
                                $this->metadata['book'] = $RDF->c($line['d_r1']);
                            }
                        $link = '<a href="' . PATH . COLLECTION . '/v/' . $line['d_r2'] . '" class="summary_a">';
                        $linka = '</a>';
                        $value = '<p class="summary_ln">' . $link . $RDF->c($line['d_r2']) . $linka . '</p>';
                        $this->lets('summary', $value);
                        break;
                    case 'hasClassificationAncib':
                        $this->lets('CatAncib', $value);
                        break;
                    case 'hasSectionOf':
                        $this->lets('Section', $value);
                        break;
                    case 'hasFileStorage':
                        $this->let('PDF', $value);
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
                        $this->lets('abstract', $valueO . ' @' . $langO);
                        break;
                    case 'hasAuthor':
                        $name = '<a class="summary_a" href="' . URL . COLLECTION . '/v/' . $ddv2 . '">' . $value . '</a><sup>(org.)</sup>';
                        $this->lets('authors', $name.'$');
                        break;
                    case 'dateOfPublication':
                        $this->lets('year', $value);
                        break;
                    case 'hasOrganizator':
                        $name = '<a class="summary_a" href="' . URL . COLLECTION . '/v/' . $ddv2 . '">' . $value . '</a><sup>(org.)</sup>';
                        $this->lets('authors', $name.'$');
                        break;
                    case 'isPublisher':
                        $this->lets('editora', $value);
                        break;
                    case 'hasSectionOf':
                        $this->lets('section', $value);
                        break;
                    case 'hasCover':
                        $cover = $COVER->image($ddv2);
                        $this->lets('cover', $cover);
                        break;
                    case 'hasPage':
                        $this->lets('pages', $value);
                        break;
                    case 'isPlaceOfPublication':
                        $this->lets('editora_local', $value);
                        break;
                    case 'isPubishIn':
                        $this->lets('source', $value);
                        $this->lets('id_jnl', $ddv2);
                        break;
                    case 'hasLanguageExpression':
                        $this->lets('idioma', $value);
                        break;
                    case 'hasTitle':
                        $valueO = trim(strip_tags($valueO));
                        $this->lets('title', $valueO);
                        $this->lets('idioma', $langO);
                        break;
                    case 'hasSubject':
                        $this->lets('keywords', $value);
                        $this->lets('subject', $value);
                        break;
                    default:

                        break;
                }
            }
        }
        return $this->metadata;
    }

    function dc($meta)
    {

        if (isset($meta['concept'])) {
            $concept = $meta['concept'];

            $metadata = array();
            $m = '';

            foreach ($concept as $class => $value) {
                switch ($class) {
                    case 'c_class':
                        $this->let('keywords', $value);
                        $this->let('DC.Type', $value);
                        $this->let('DC.Type.articleType', $value);
                        break;
                    case 'id_c':
                        $this->let('DC.Identifier', 'Brapci-'.$value);
                        $this->let('url', PATH.'/v/'.$value);
                        $this->let('DC.Identifier.URI', PATH.'/v/'.$value);
                        break;
                    case 'n_name':
                        $this->let('DC.Identifier', $value);
                        break;
                    case 'cc_created':
                        $date = $value;
                        $m .= '<meta name="DC.Date.created" scheme="ISO8601" content="' . $date . '"/>' . cr();

                        $this->let('citation_date', $value);
                        break;
                    default:
                        break;
                }
            }
        }

        /*************************************************************************/
        if (isset($meta['data'])) {
            $data = $meta['data'];

            for ($r = 0; $r < count($data); $r++) {
                $line = $data[$r];
                $class = $line['c_class'];
                $value = $line['n_name2'];
                $lang = $line['n_lang2'];
                $valueO = $line['n_name'];
                $langO = $line['n_lang'];

                switch ($class) {
                    case 'Identifier.DOI':
                        $this->let('DC.Description', $value.'##xml:lang='.$lang);
                        break;
                    case 'hasAbstract':
                        $this->let('DC.Description', $valueO.'@'.$langO);
                        break;
                    case 'hasAuthor':
                        $this->let('DC.Creator.PersonalName', $value);
                        break;
                    case 'hasTitle':
                        $valueO = strip_tags($valueO);
                        $this->let('title', $valueO);
                        if (!isset($this->metadata['DC.Title']))
                            {
                                $this->let('DC.Title', $valueO);
                                $this->title = $valueO;
                                $this->lang = $langO;
                            } else {
                                $this->let('DC.Title.Alternative', $valueO);
                            }

                        break;
                    case 'hasSubject':
                        $this->let('DC.Subject', $value);
                        $this->let('keywords', $value);
                        break;
                    default:

                        break;
                }
            }
        }

        foreach($this->metadata as $meta=>$value)
            {
                for($v=0;$v < count($value);$v++)
                    {
                        $vlr = $value[$v];
                        $m .= '<meta name="'.$meta.'" content="'.$vlr.'"/>'.cr();
                    }
            }
        return $m;
    }
}
