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
        $this->metadata[$class] .= $value . ' ';
        return true;
    }

    function metadata($meta)
    {
        $this->metadata = array();
        if (isset($meta['concept'])) {
            $concept = $meta['concept'];

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
                $ddv2 = $line['d_r2'];

                switch ($class) {
                    case 'Identifier.DOI':
                        $this->lets('DOI', $value . '##xml:lang=' . $lang);
                        break;
                    case 'hasAbstract':
                        $this->lets('abstract', $valueO . ' @' . $langO);
                        break;
                    case 'hasAuthor':
                        $this->lets('authors', $value);
                        break;
                    case 'hasTitle':
                        $valueO = strip_tags($valueO);
                        $this->lets('title', $valueO);
                        break;
                    case 'hasSubject':
                        $this->lets('keywords', $value);
                        break;
                    case 'hasSectionOf':
                        $this->lets('section', $value);
                        break;
                    case 'isPubishIn':
                        $this->lets('source', $value);
                        $this->lets('id_jnl', $ddv2);
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
