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
        if (is_array($value))
            {
                $this->metadata[$class] = $value;
            } else {
                $this->metadata[$class][] = trim($value);
            }
        return true;
    }

    function leta($class, $value, $array)
    {
        if(!isset($this->metadata[$class]))
            {
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
        if (!isset($this->metadata[$class]))
            {
                $this->metadata[$class] = '';
            }

            if ($this->metadata[$class] != '')
            {
                $this->metadata[$class] .= ' ';
            }
        $this->metadata[$class] .= trim($value);
        return true;
    }


    function metadata($meta,$erros=false)
    {
        $RDF = new \App\Models\Rdf\RDF();
        $COVER = new \App\Models\Base\Cover();
        $ISBN = new \App\Models\ISBN\Index();
        $Source = new \App\Models\Base\Sources();

        $LDL_title = '';
        $LDL_author = '';
        $LDL_section = '';

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
                $class = trim($line['c_class']);
                $value = $line['n_name2'];
                $lang = $line['n_lang2'];
                $valueO = $line['n_name'];
                $langO = $line['n_lang'];
                $ddv1 = $line['d_r1'];
                $ddv2 = $line['d_r2'];

                if (($class == 'hasRegisterId') and (substr($valueO,0,3) == '10.'))
                    {
                        $class = 'hasDOI';
                        $line['n_name2'] = $valueO;
                    }

                switch ($class) {
                    case 'hasBookChapter':
                        $type = $this->metadata['Class'];
                        if ($type=='Book')
                            {
                                $db = $RDF->le($line['d_r1']);
                                //$this->metadata['book'] = $RDF->c($line['d_r1']);
                            } elseif ($type == 'BookChapter')
                            {
                                $this->metadata['bookID'] = $line['d_r1'];
                                $this->lets('books', $RDF->c($line['d_r1']));
                            }

                        $link = '<a href="' . PATH . COLLECTION . '/v/' . $line['d_r2'] . '" class="summary_a">';
                        $linka = '</a>';

                        $bn = $RDF->directory($line['d_r2']);

                        if (file_exists($bn.'name.nm'))
                            {
                                $value = '<p class="summary_ln">' . $link . $RDF->c($line['d_r2']) . $linka . '</p>';
                            } else {
                                $value = 'Aguarde, em processamento<br><br>';
                            }
                        $this->lets('summary', $value);
                        break;
                    case 'hasClassificationAncib':
                        $this->lets('CatAncib', $value);
                        $this->let_array('CatAncibArray', $ddv2, $value);
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
                        $this->lets('abstract', $valueO.'@'.$langO);
                        $this->leta('Abstract', $valueO, $langO);
                        break;
                    case 'hasLicense':
                        $value = '<img src="' . URL . '/img/icons/cc/' . $value. '.png" style="max-height: 45px;">';
                        $this->lets('license', $value);
                        break;
                    case 'hasAuthor':
                        $name = '<a class="summary_a" href="' . URL . '/autoridade/v/' . $ddv2 . '">' . $value . '</a>';
                        $this->lets('authors', $name.'$');
                        $this->let('Authors', $value . ';' . $ddv2);
                        $this->let('AuthorsOf', $ddv1);
                        if ($LDL_author != '') {
                            $LDL_author .= '; '; }
                        $LDL_author .= nbr_author($value,7);
                        break;
                    case 'hasOrganizator':
                        $name = '<a class="summary_a" href="' . URL .  '/autoridade/v/' . $ddv2 . '">' . $value . '</a><sup>(org.)</sup>';
                        $this->lets('authors', $value . ';' . $ddv2);
                        $this->let('Authors', $value . '<sup>Org.</sup>;' . $ddv2);
                        break;
                    case 'dateOfPublication':
                        $this->lets('year', $value);
                        break;
                    case 'isPublisher':
                        $this->lets('editora', $value);
                        break;
                    case 'hasSectionOf':
                        $this->lets('section', $value . '$');
                        $this->let('Sections', $value . ';' . $ddv2);
                        if ($LDL_section != '')
                        {
                            $LDL_section .= ' - ';
                        }
                        $LDL_section .= trim($value);
                        break;
                    case 'hasCover':
                        $cover = $COVER->image($ddv2);
                        $this->lets('cover', $cover);
                        break;
                    case 'hasPage':
                        $this->lets('Pages', $value);
                        break;
                    case 'isPlaceOfPublication':
                        $this->lets('editora_local', $value);
                        break;
                    case 'isPubishIn':
                        $this->lets('Journal', $value);
                        $this->lets('jnl_frbr', $ddv2);
                        $ds = $Source->where('jnl_frbr',$ddv2)->first();
                        if ($ds != '')
                        {
                            $this->lets('id_jnl', $ds['id_jnl']);
                        }
                        break;
                    case 'hasLanguageExpression':
                        $this->lets('idioma', $value);
                        break;
                    case 'hasTitle':
                        $valueO = trim(strip_tags($valueO));
                        $this->lets('title', $valueO);
                        $this->lets('idioma', $langO);
                        $this->leta('Title',$valueO,$langO);
                        break;
                    case 'hasSubject':
                        $this->leta('Keywords', $lang, $value.';'.$ddv2);
                        $this->let('DC.Subject', $value);
                        if ($value != '')
                        {
                            $this->lets('keywords', anchor(PATH.COLLECTION.'/v/'.$ddv2,$value) . '.');
                        }
                        break;
                    case 'hasIssueOf':
                        if (!isset($issue_proceessed[$ddv1]))
                            {
                                $IssueWorks = new \App\Models\Base\IssuesWorks();
                                $dti = $IssueWorks->select("siw_journal")->where('siw_issue',$ddv1)->first();
                                if (isset($dti['siw_journal']))
                                    {
                                        $this->lets('id_jnl', $dti['siw_journal']);
                                    }
                                $this->let('issue_id', $ddv1);
                                $issue_proceessed[$ddv1] = 1;
                            }
                        break;
                    case 'hasIssueOf':
                        /************** ARTIGO */
                        if (!isset($issue_proceessed[$ddv1]) or (count($issue_proceessed) == 0)) {
                            $issue = $ddv1;
                            $journal = $ddv2;
                            $this->let('issue_id', $issue);
                            $issue_proceessed[$ddv1] = 1;
                        } else {
                            echo "OPS";
                            exit;
                        }
                        break;
                    case 'hasPublicationNumber':
                        $this->lets('issue_nr', $value);
                        break;
                    case 'hasPublicationVolume':
                        $this->lets('issue_vol', $value);
                        break;
                    case 'prefLabel':
                        $this->lets('prefLabel', $valueO);
                        break;
                    case 'altLabel':
                        $this->let('issue_name', $valueO, $langO);
                        $this->let_array('altLabels', $ddv1, $valueO);
                        break;
                    case 'hiddenLabel':
                        $this->let('hiddenLabel', $value . $valueO);
                        break;
                    case 'hasPageStart':
                        $this->lets('pagi', $value);
                        break;
                    case 'hasPageEnd':
                        $this->lets('pagf', $value);
                        break;
                    case 'hasPlace':
                        $this->lets('place',$value);
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
                        $this->let('ISSUE', $ddv1);
                        break;
                    case 'hasIdRegister':
                        break;
                    case 'hasEditor':
                        $this->let('Editor', anchor(PATH. '/autoridade/v/'.$ddv2,$value));
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
                        $this->let_array('Imagem', $valueO,$ddv1);
                        break;
                    case 'fullText':
                        $this->let_array('Fulltext', $valueO, $ddv1);
                        break;
                    case 'hasGoogleSchollarId':
                        $this->let_array('Google', $valueO, $ddv1);
                        break;
                    default:
                        if ($erros == true)
                        {
                            echo '=Not identify class=>'.$class.' == []'. $value.'('.$ddv1.') [O]'.$valueO.'('.$ddv2.')<br>';
                        }
                        break;
                }
            }
        }

        /******************************* LEGEND */
        if (isset($this->metadata['Journal']))
            {
                $legend = trim($this->metadata['Journal']);
                $journal = trim($this->metadata['Journal']);
            } else {
                $legend = '';
                $journal= '';
            }

        if (isset($this->metadata['Issue']['Issue_roman'])) {
            $legend .= ', v.' . $this->metadata['Issue']['Issue_roman'];
        }
        if (isset($this->metadata['Issue']['Issue_nr']))
            {
                $legend .= ', n.' . $this->metadata['Issue']['Issue_nr'];
            }
        if (isset($this->metadata['Issue']['Year']))
            {
                $legend .= ', ' . $this->metadata['Issue']['Year'];
            }


        if (isset($this->metadata['title'])) {
            if (isset($this->metadata['Title']))
                {
                if(isset($this->metadata['Title']['pt-BR']))
                    {
                        $this->metadata['difusion']['LDL_title'] = nbr_title($this->metadata['Title']['pt-BR']);
                        $this->metadata['difusion']['LDL_lang'] = 'pt-BR';
                    }
                elseif(isset($this->metadata['Title']['en']))
                    {
                        $this->metadata['difusion']['LDL_title'] = nbr_title($this->metadata['Title']['en']);
                        $this->metadata['difusion']['LDL_lang'] = 'en';
                    }
                elseif(isset($this->metadata['Title']['es']))
                    {
                        $this->metadata['difusion']['LDL_title'] = nbr_title($this->metadata['Title']['es']);
                        $this->metadata['difusion']['LDL_lang'] = 'es';
                    }
                elseif (isset($this->metadata['Title']['fr'])) {
                    $this->metadata['difusion']['LDL_title'] = nbr_title($this->metadata['Title']['fr']);
                    $this->metadata['difusion']['LDL_lang'] = 'fr';
                }               elseif (isset($this->metadata['Title']['es-ES'])) {
                    $this->metadata['difusion']['LDL_title'] = nbr_title($this->metadata['Title']['es-ES']);
                    $this->metadata['difusion']['LDL_lang'] = 'es';
                }
                else
                {
                    echo "=========LANGUAGE=========";
                    pre($this->metadata['Title']);
                }
                }

        } else {
            $this->metadata['difusion']['LDL_title'] = '::Sem ´título::';
        }
        $this->metadata['difusion']['LDL_author'] = $LDL_author;
        $this->metadata['difusion']['LDL_legend'] = $legend;
        $this->metadata['difusion']['LDL_journal'] = $journal;
        $this->metadata['difusion']['LFL_section'] = trim($LDL_section);

        if (isset($meta['concept']['id_cc']))
            {
                $this->metadata['ID'] = $meta['concept']['id_cc'];
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
