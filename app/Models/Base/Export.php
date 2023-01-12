<?php

namespace App\Models\Base;

use CodeIgniter\Model;

class Export extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'exports';
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

    function index($d1,$d2,$d3)
        {
            $sx = 'EXPORT '.$d1;

            switch($d1)
                {
                    case 'articles':
                        $sx = $this->export_articles($d2,$d3);
                        break;
                    default:
                        $sx = bsc($this->menu(),12);
                        break;
                }
            $sx = bs($sx);
            return $sx;
        }

    function menu()
    {
        $sx = '';
        $menu = array();
        $mod = 'export';
        $menu['#brapci.EXPORT_ELASTIC'] = '#';
        $menu[PATH.'admin/' . $mod . '/articles'] = lang('brapci.export').' '.lang('brapci.articles');
        $menu[PATH . 'admin/' . $mod . '/proceeding'] = lang('brapci.export') . ' ' . lang('brapci.proceeding');
        $menu[PATH . 'admin/' . $mod . '/books'] = lang('brapci.export') . ' ' . lang('brapci.books');
        $sx = menu($menu);
        $sx = bs(bsc($sx));
        return $sx;
    }

    function export_articles($d1,$d2)
        {
            $offset = round($d1);
            $limit = 10;
            $class = 'Article';
            $type = 'JA';
            $sx = $this->export_data($class, $type, $offset, $limit);
            return $sx;
        }

    function export_data($class,$type,$offset,$limit)
        {
            $RDF = new \App\Models\RDF\Rdf();
            $RDFClass = new \App\Models\RDF\RdfClass();
            $RDFConcept = new \App\Models\RDF\RDFConcept();
            $Metadata = new \App\Models\Base\Metadata();
            $ElasticRegister = new \App\Models\ElasticSearch\Register();

            $id = $RDFClass->Class($class,false);

            $ids = $RDFConcept->where('cc_class',$id)->findAll($limit,$offset);
            $sx = '';
            $sx .= 'Export '.($offset+1);
            $sx .= '<ul>';
            for ($r=0;$r < count($ids);$r++)
                {
                    $line = $ids[$r];
                    $idr = $line['id_cc'];

                    $dir = $RDF->directory($idr);
                    $file = $dir . 'article.json';

                    if (!file_exists($file))
                        {
                            $RDF->c($idr);
                        }

                    /*
                    $json = file_get_contents($file);
                    $json = (array)json_decode($json);
                    $json['type'] = $type;
                    $json['collection'] = substr($class,0,1);
                    pre($json);
                    $sx .= '<li>' . strzero($idr, 8) . ' ' . $ElasticRegister->data($idr, $json) . ' (' . $dir . ')</li>';
                    */

                    $line = $RDF->le($idr);
                    $Metadata->metadata = array();
                    $Metadata->metadata($line);
                    $meta = $Metadata->metadata;
                    $meta['collection'] = substr($class,0,1);
                    $meta['fulltext'] = '';
                    $meta['year'] = '';
                    $meta['pdf'] = 0;
                    $sx .= '<li>'.strzero($meta['article_id'],8).' '.$ElasticRegister->data($idr,$meta).' ('.$dir.')</li>';
                }
            $sx .= '</ul>';
            return $sx;
        }
}
