<?php

namespace App\Models\Guide\Manual;

use CodeIgniter\Model;

class Content extends Model
{
    protected $DBGroup          = 'guide';
    protected $table            = 'guide_content';
    protected $primaryKey       = 'id_gc';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_gc',
        'gc_guide',
        'gc_type',
        'gc_subsection',
        'gc_title',
        'gc_content',
        'gc_order',

    ];
    var $typeFields    = [
        'hidden',
        'sql:id_g:g_name:guide*',
        'sql:type_cod:type_description:guide_content_type where type_header=1 *',
        'string',
        'string',
        'hidden',
        '[0:99]*',

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

    var $path = PATH.'/guide/content';
    var $path_back = PATH . '/guide/manual/viewid/';
    var $id = 0;

    function index($d1,$d2='',$d3='',$d4='')
        {
            switch($d1)
                {
                    case 'reord':
                        $sx = $this->reord($d2);
                        break;
                    case 'upload':
                        $sx = $this->form_img($d2);
                        break;
                    case 'edit':
                        $sx = $this->edit($d2,$d3,$d4);
                        break;
                    default:
                        $sx = $this->list($d2);
                        $sx .= $this->bnt_new($d2);
                        $sx .= $this->bnt_reord($d2);
                        break;
                }
            return $sx;
        }

    function reord($id)
        {
        $dt = $this
            ->join('guide_content_type', 'gc_type = type_cod')
            ->where('gc_guide', $id)
            ->where('type_header', 1)
            ->orderBy('gc_order')
            ->findAll();

            $ord = 1;
            foreach($dt as $idx=>$line)
                {
                    $data['gc_order'] = $ord++;
                    $this->set($data)->where('id_gc', $line['id_gc'])->update();
                }
            $sx = metarefresh(PATH. '/guide/manual/viewid/'.$id);
            return $sx;
        }

    function edit($d2,$d3,$d4)
        {
            $Guide = new \App\Models\Guide\Manual\Index();
            $d2 = round('0' . $d2);
            $this->id = $d2;
            if ($d2 == 0)
                {
                    $_POST['gc_guide'] = $Guide->getGuide();
                }
            $guide = 1;
            $this->typeFields[3] = 'sql:id_gc:gc_title:guide_content where gc_type like "H%" and gc_guide = ' . $guide . '';
            $sx = bs(bsc(form($this), 12));
            return $sx;
        }

    function bnt_new($id)
        {
            $sx = anchor(PATH. '/guide/content/edit/0?gc_guide='.$id,lang('guide.new_content'),['class'=>'btn btn-outline-primary me-2']);
            return $sx;
        }

    function bnt_reord($id)
    {
        $sx = anchor(PATH . '/guide/content/reord/' . $id, lang('guide.reord_content'), ['class' => 'btn btn-outline-primary me-2']);
        return $sx;
    }

    function export($id)
        {
          $txt = $this->export_content($id);

            $xhtml = troca($xhtml,'$context',$txt);
            $dir = '_repository/guide/'.$id.'/export/';
            dircheck($dir);
            $file = $dir.'guide.xhtml';
            file_put_contents($file,$xhtml);
            $sx = 'wget '.anchor(PATH.'/'.$file);
            $sx = bs(bsc($sx));
            return $sx;
        }

    function export_content($id)
        {
            $Block = new \App\Models\Guide\Manual\Block();
            $id = 1;
            $dt = $this
                ->join('guide_content_type', 'gc_type = type_cod')
                ->where('gc_guide', $id)
                ->where('type_header', 1)
                ->where('gc_active', 1)
                ->orderBy('gc_order')
                ->findAll();
            $summary = '<ul>';
            $sm = [];
            foreach($dt as $id=>$line)
                {
                    $tag = ascii($line['gc_title']);
                    $tag = troca($tag,array('(',')'),'');
                    $tag = mb_strtolower($tag);
                    $tag = troca($tag,' ','_');
                    $nl = sonumero($line['gc_type']);
                    $label = anchor('#'.$tag,h($line['gc_title'],$nl));
                    $sm[$line['id_gc']] = '<a name="'.$tag.'">'.h($line['gc_title'],$nl).'</a>';
                    switch($nl)
                        {
                            case 1:
                                $summary .= '<li>' . $label . '</li>'.cr();
                                break;
                            case 2:
                                $summary .= '<ul><li>' . $label . '</li></ul>' . cr();
                                break;
                            case 3:
                                $summary .= '<ul><li><ul><li>' . $label . '</li></ul></li></ul>' . cr();
                                break;
                        }

                }
            $summary .= '</ul>';
            $sx = $summary;
            $sx .= '<hr>';

            $cnt = '';

            foreach($sm as $ids=>$title)
                {
                    $dtc = $this
                        ->where('gc_subsection', $ids)
                        ->where('gc_active', 1)
                        ->orderBy('gc_order')
                        ->findAll();

                    if (count($dtc) > 0)
                        {
                            $cnt .= $title;
                        }

                    foreach($dtc as $idd=>$lined)
                        {
                            $cnt .= $Block->display($lined);
                        }
                }

            $sx .= $cnt;

            return $sx;
        }

    function list($id)
        {
            $sx = '';
            $sub = 0;
            $id = round('0'.$id);
            if ($id == 0)
                {
                    return '';
                }
            $dt = $this
                ->join('guide_content_type', 'gc_type = type_cod')
                ->where('gc_guide',$id)
                ->where('type_header', 1)
                ->where('gc_active', 1)
                ->orderBy('gc_order')
                ->findAll();
            $sx .= '<table class="table full">';
            foreach($dt as $id=>$line)
                {
                    $ext = '';
                    $type = $line['gc_type'];
                    $sx .= '<tr>';
                    $sx .= '<td>';
                    $sx .= $type;
                    $sx .= '</td>';
                    $sx .= '<td>';
                    $sx .= $line['gc_order'];
                    $sx .= '</td>';
                    $sx .= '<td>';
                    $label = anchor(PATH.'/guide/block/viewid/'.$line['id_gc'], $line['gc_title']);
                    switch($type)
                        {
                            case 'H1':
                                $sx .= h($label,1);
                                break;
                            case 'H2':
                                $sx .=  h($label, 2);
                                break;
                            case 'H3':
                                $sx .=  h($label, 3);
                                break;
                            case 'H4':
                                $sx .=  h($label, 4);
                                break;
                            case 'H5':
                                $sx .=  h($label, 5);
                                break;
                            default:

                        }
                    $sx .= '</td>';
                    $sx .= '<td>';
                    $sx .= anchor(PATH . '/guide/content/edit/' . $line['id_gc'], bsicone('edit'));
                    $sx .= $ext;
                    $sx .= '</td>';
                    $sx .= '<tr>';
                }
            $sx .= '</table>';
            return $sx;
        }
}
