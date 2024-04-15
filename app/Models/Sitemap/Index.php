<?php

namespace App\Models\Sitemap;

use CodeIgniter\Model;

class Index extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'indices';
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
            $sx = h('SITEMAP');
            switch($d1)
                {
                    case 'create':
                        $sx .= $this->create();
                        break;
                    default:
                        $sx .= $this->menu();
                        $sx = $sx;
                        break;
                }
            return bs(bsc( $sx, 12));
        }

    function menu()
        {
            $M = [];
            $M[PATH.'admin/sitemap/create'] = "Create SiteMap";
            return menu($M);
        }

    function create()
        {
            $Elastic = new \App\Models\ElasticSearch\Register();
            $loop = true;
            $offset = 0;
            $limit = 40000;
            $sitemap = '';
            $idn = 1;
            while($loop)
                {
                    $dt = $Elastic
                        ->where('`use`',0)
                        ->findAll($limit,$offset);
                    $i = 0;
                    $sx = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">' . cr();
                    foreach($dt as $id=>$line)
                        {
                            $sx .= '<url>'.cr();
                            $url = PATH.'#/v/'.$line['ID'];
                            $date = substr($line['create_at'],0,10);
                            $sx .= '<loc>'.$url.'</loc>'.cr();
                            $sx .= '<lastmod>' . $date . '</lastmod>' . cr();
                            $sx .= '</url>' . cr();
                            $offset++;
                            $i++;
                        }
                    $sx .= '</urlset>';
                    file_put_contents('sitemap_'.strzero($idn,2).'.xml', $sx);
                    $idn++;
                    if (($i != $limit) or ($dt == []))
                        {
                            $loop = false;
                        }
                }

            $link = '<a href="'.PATH.'sitemap.xml">SiteMap.xml</a>';

            $sx = 'Sitemap was create at '.$link;
            return $sx;
        }
}
