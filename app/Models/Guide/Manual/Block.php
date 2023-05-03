<?php

namespace App\Models\Guide\Manual;

use CodeIgniter\Model;

class Block extends Model
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
        'gc_active'

    ];
    var $typeFields    = [
        'hidden',
        'sql:id_g:g_name:guide*',
        'sql:type_cod:type_description:guide_content_type where type_header <> 1 *',
        'string',
        'hidden',
        'text*',
        '[0:99]*',
        'set:1'

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

    var $path = PATH.'/guide/block';
    var $path_back = PATH . '/guide/block/viewid/';
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
                    case 'viewid':
                        if ($d2=='')
                            {
                                $d2 = $this->getBlock();
                            }
                        $this->setBlock($d2);
                        $sx = $this->list($d2);
                        $sa = $this->bnt_new($d2);
                        $sa .= $this->bnt_reord($d2);
                        $sa .= $this->bnt_return($d2);
                        $sx = bs(bsc($sa)).$sx;
                        break;
                    default:
                        $sx = $this->list($d2);

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
                    $dc = $this
                            ->select('count(*) as total, gc_subsection')
                            ->where('gc_subsection', $d2)
                            ->groupby('gc_subsection')
                            ->orderby('gc_subsection')
                            ->first();
                    $_POST['gc_order'] = $dc['total'];
                }
            $guide = 1;
            $this->typeFields[1] = 'set:' . $Guide->getGuide();
            $this->typeFields[3] = 'set:'.$this->getBlock();
            $sx = bs(bsc('Sub:'. $this->typeFields[3],12));
            $sx .= bs(bsc(form($this), 12));
            return $sx;
        }

    function setBlock($id)
        {
            $_SESSION['block'] = $id;
        }
    function getBlock()
    {
        $id = 0;
        if (isset($_SESSION['block']))
            {
                $id = $_SESSION['block'];
            }
        return $id;
    }

    function form_img($id)
        {
            $sx = '';

            if (isset($_FILES['images']))
                {
                    $dt = $this->find($id);
                    $tmp = $_FILES['images']['name'];
                    $type = $_FILES['images']['type'];
                    $tmp_name = $_FILES['images']['tmp_name'];
                    $error = $_FILES['images']['error'];
                    if (($error == 0) and (strlen($tmp) > 0))
                        {
                            $name = $dt['gc_content'];
                            $file = '_repository/guide/'.$dt['gc_guide'].'/';
                            dircheck($file);
                            $file .= $name;
                            echo '<hr>';
                            echo $file;
                            echo '<hr>';
                            echo $tmp_name;
                            move_uploaded_file($tmp_name,$file);
                            echo wclose();
                        }
                }
            $sx .= '=>'.$id;
            $sx .= form_open_multipart();
            $sx .= form_upload('images');
            $sx .= '<br/><br/>';
            $sx .= form_submit('action',lang('guide.upload'));
            $sx .= form_close();
            echo $sx;
            exit;
        }

    function bnt_new($id)
        {
            $sx = anchor(PATH. '/guide/block/edit/0?gc_guide='.$id,lang('guide.new_content'),['class'=>'btn btn-outline-primary me-2']);
            return $sx;
        }

    function bnt_reord($id)
    {
        $sx = anchor(PATH . '/guide/block/reord/' . $id, lang('guide.reord_content'), ['class' => 'btn btn-outline-primary me-2']);
        return $sx;
    }

    function bnt_return($id)
    {
        $sx = anchor(PATH . '/guide/manual/viewid/', lang('guide.return_index'), ['class' => 'btn btn-outline-danger me-2']);
        return $sx;
    }

    function display($line)
        {
        $sx = '';
        $type = $line['gc_type'];
        switch ($type) {
            case 'P':
                $sx .=  '<p>' . $line['gc_content'] . '</p>';
                break;
            case 'IMG':
                $file = '_repository/guide/' . $line['gc_guide'] . '/' . $line['gc_content'];
                if (!is_file($file)) {
                    $sx .= $file;
                    $file = 'img/guide/noimage.jpg';
                }

                $sx .=  '<img src="' . URL . '/' . $file . '" class="img-fluid">';
                $ext =  '<span class="handler" onclick="newwin(\'' . PATH . '/guide/block/upload/' . $line['id_gc'] . '\',800,600);">' . bsicone('upload') . '</span>';
                break;
            default:
                $sx .= '<p>Not found:'.$type.'<p>';
                break;
        }
        return $sx.cr();
        }

    function list($id)
        {
            /************************************** */
            if (get('delete') != '')
                {
                    $data['gc_active'] = 0;
                    $idd = round(get('delete'));
                    $this->set($data)->where('id_gc',$idd)->update();
                }

            $id = round('0' . $id);
            if ($id == 0) {
                return '';
            }

            $dc = $this->find($id);
            $this->setBlock($id);

            $sx = '';
            $sx .= h($dc['gc_title']);
            $dt = $this
                ->join('guide_content_type', 'gc_type = type_cod')
                ->where('gc_subsection',$id)
                ->where('type_header', 0)
                ->where('gc_active', 1)
                ->orderBy('gc_order')
                ->findAll();
            $sx .= '<table class="table full">';
            $sx .= '<tr>
                    <th width="1%">#</th>
                    <th width="5%">ord.</th>
                    <th width="10%">type</th>
                    <th width="79%">content</th>
                    <th width="5%">act.</th>
                    </tr>';
            $nr = 1;
            foreach($dt as $id=>$line)
                {
                    $ext = '';
                    $type = $line['gc_type'];
                    $sx .= '<tr>';
                    $sx .= '<td>';
                    $sx .= $nr++;
                    $sx .= '</td>';

                    $sx .= '<td>';
                    $sx .= $line['gc_order'];
                    $sx .= '</td>';

                    $sx .= '<td>';
                    $sx .= $type;
                    $sx .= '</td>';

                    $sx .= '<td>';
                    $label = anchor(PATH.'/guide/block/viewid/'.$line['id_gc'], $line['gc_title']);

                    $ext .=  '<span class="handler text-danger" onclick="if (confirm(\''.lang('guide.exclude'). '\') == true) '.cr();
                    $ext .=  '{ window.location.href = \'' . PATH . '/guide/block/viewid/'.$this->getBlock().'?delete=' . $line['id_gc'] . '\'; }'.cr();
                    $ext .=  '">' . bsicone('trash') . '</span>';

                    $sx .= '</td>';
                    $sx .= '<td>';
                    $sx .= anchor(PATH . '/guide/block/edit/' . $line['id_gc'], bsicone('edit'));
                    $sx .= $ext;
                    $sx .= '</td>';
                    $sx .= '<tr>';
                }
            $sx .= '</table>';
            $sx = bs(bsc($sx, 12));
            return $sx;
        }
}
