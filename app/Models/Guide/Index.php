<?php

namespace App\Models\Guide;

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

    function index($d1='',$d2='',$d3='')
        {

            $sx = '';

            switch($d1)
                {
                    case 'dataverse':
                        $sx = $this->installDataverse();
                        break;
                    case 'popup':
                        $Course = new \App\Models\Guide\Course\Index();
                        $sx .= $Course->index($d2, $d3);
                        break;
                    case 'content':
                        $Content = new \App\Models\Guide\Manual\Content();
                        $sx .= $Content->index($d2, $d3);
                        break;
                    case 'export':
                        $Content = new \App\Models\Guide\Manual\Content();
                        $sx .= $Content->export($d2, $d3);
                        break;
                    case 'block':
                        $Block = new \App\Models\Guide\Manual\Block();
                        $sx .= $Block->index($d2, $d3);
                        break;
                    case 'manual':
                        $Manual = new \App\Models\Guide\Manual\Index();
                        $sx .= $Manual->index($d2, $d3);
                        break;
                    case 'course':
                        $Course = new \App\Models\Guide\Course\Index();
                        $sx .= $Course->index($d2,$d3);
                        break;
                    default:
                        $menu = [];
                        $menu[PATH.'/guide/course/'] = lang('brapci.course');
                        $menu[PATH . '/guide/manual/'] = lang('brapci.manual');
                        $menu[PATH . '/guide/dataverse/'] = lang('brapci.dataverse_install');
                        $sx .= menu($menu);
                        $sx = bs(bsc($sx));
                }


            return $sx;
        }

        function installDataverse()
            {
                $sx = h('Install Dataverse');

                $sx .= h('Criando a variável como o guia no Dataverse',2);
                $sx .= '<p>Paramentro: :NavbarGuidesUrl</p>';
                $sx .= '<pre>curl -X PUT -d https://venus.brapci.inf.br/guide.xhtml http://localhost:8080/api/admin/settings/:NavbarGuidesUrl</pre>';

                $sx .= '<p>Acesse o diretório com a localização dos arquivos</p>';
                $sx .= '<pre>cd /usr/local/payara5/glassfish/domains/domain1/applications/dataverse</pre>';

                $sx = bs(bsc($sx));
                return $sx;
            }
}
