<?php

namespace App\Models\Dataverse;

use CodeIgniter\Model;

class Translate extends Model
{
    protected $DBGroup          = 'dataverse';
    protected $table            = 'translate';
    protected $primaryKey       = 'id_dvn';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_dv', 'dvn_file','dvn_field','dvn_en','dvn_pt'
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

    function index($d1,$d2,$d3)
        {
            $sx = '';
            switch($d1)
                {
                    case 'upload':
                        $sx = $this->upload();
                        break;
                    case 'file':
                        $_SESSION['translate'] = $d3;
                        $sx .= $this->view($d3);
                        break;
                    case 'mass':
                        $_SESSION['translate'] = $d3;
                        $sx .= $this->mass($d3);
                        break;
                    case 'export':
                        $_SESSION['translate'] = $d3;
                        $sx .= $this->download($d3);
                        break;
                    default:
                        $sx .= $this->actions();
                        $sx .= $this->files();
                        break;
                }
            return $sx;
        }

    function download($fn)
        {
            $sx = '';
            $dir = '_repository/translate/';
            $file = $dir.$fn;
            echo $file;
            if (file_exists($file))
                {
                    $dt = $this
                        ->where('dvn_file', $fn)
                        ->findAll();

                    $dd = [];
                    foreach($dt as $id=>$line)
                        {
                            $field = $line['dvn_field'];
                            $dd[$field] = $line['dvn_pt'];
                        }
                    $txt = file_get_contents($file);
                    $ln = explode(chr(10),$txt);
                    $tx = '';
                    foreach($ln as $id=>$l)
                        {
                            $c = substr($l,0,1);
                            switch($c)
                                {
                                    case '#':
                                        $tx .= $l.chr(10);
                                        break;
                                    default:
                                        if (($pos=strpos($l,'=')) > 0)
                                            {
                                                $lb = trim(substr($l,0,$pos));
                                                if (isset($dd[$lb]))
                                                    {
                                                        $tx .= $lb.'='.$dd[$lb] . chr(10);
                                                    } else {
                                                        $tx .= $l.chr(10);
                                                    }
                                            } else {
                                                $tx .= $l.chr(10);
                                            }
                                        break;
                                }
                        }
                    $fileD = troca($file, '.properties', '_pt.properties');
                    file_put_contents($fileD,$tx);
                    file_put_contents($fileD.'_utf8', utf8_decode($tx));
                    $sx .= bsmessage(lang('brapci.exported_success'));
                    $sx .= '<ul>';
                    $sx .= '<li>'.anchor(URL.'/'.$fileD,$fileD).'</li>';
                    $sx .= '<li>' . anchor(URL . '/' .$fileD . '_utf8', $fileD . '_utf8').'</li>';
                    $sx .= '</ul>';
                } else {
                    $sx .= bsmessage('File not found '.$file);
                }
            return $sx;
        }

    function upload()
        {
            $sx = '';
            $sx .= form_open_multipart();
            $sx .= form_upload('file');
            $sx .= form_submit('submit',lang('brapci.send'));
            $sx .= form_close();

            if (isset($_FILES['file']['tmp_name']))
                {
                    $tmp = $_FILES['file']['tmp_name'];
                    $name = $_FILES['file']['name'];
                    $type = $_FILES['file']['type'];

                    $dir = '_repository/translate/';
                    dircheck($dir);
                    $dest = $dir.$name;
                    move_uploaded_file($tmp,$dest);

                    $sx = '';

                    $sx .= $this->import($dest);

                    $sx .= bsmessage('Success',1);
                }
            return $sx;
        }

    function register($file,$field,$en,$pt='')
        {
                $dt = $this
                    ->where('dvn_file', $file)
                    ->where('dvn_field', $field)
                    ->first();
                if ($dt == '')
                    {
                        $data['dvn_file'] = $file;
                        $data['dvn_field'] = $field;
                        $data['dvn_en'] = $en;
                        if ($pt != '')
                            {
                                $data['dvn_pt'] = $pt;
                            }
                        $this->set($data)->insert();
                        $sx = '<b>inserted</b>';
                    } else {
                        $sx = '<b>already</b>';
                    }
                return $sx;
        }

    function import($dest)
        {
            $sx = '';
            $file = $dest;
            $loop = 0;
            while (strpos(' '.$file,'/') > 0)
                {
                    $loop++;
                    if ($loop > 10) { exit; }
                    $file = substr($file, strpos($file, '/') + 1, strlen($file));
                }
            $sx .= bsmessage('Importando ' . $file);

            $dt = file_get_contents($dest);
            $dt = troca($dt,chr(13),'');
            $ln = explode(chr(10),$dt);

            foreach($ln as $id=>$line)
                {
                    $p = true;
                    if (strlen(trim($line)) == 0) { $p = false; }
                    if (substr(trim($line),0,1) == '#') { $p = false; }
                    if ($p)
                        {
                            $field = trim(substr($line,0,strpos($line,'=')));
                            $en = trim(substr($line,strpos($line,'=')+1,strlen($line)));
                            if ($field != '')
                            {
                                $sx .= '<li>'.$field.' ';
                                $sx .= $this->register($file,$field,$en,'');
                                $sx .= '</li>';
                            }
                        }
                }

            return $sx;
            echo h($file);
        }

    function actions()
        {
            $sx = '';
            $sx .= anchor(PATH. '/dados/dataverse/translate/upload',bsicone('upload',32));
            $sx = bs(bsc($sx));
            return $sx;
        }

    function btn_translate($file)
        {
            $sx = '<a href="'.PATH. 'dados/dataverse/translate/mass/'.$file.'" class="btn btn-outline-primary me-2 small mb-2">'.bsicone('import').' '.lang('brapci.translate').'</a>';
            return $sx;
        }

    function btn_export($file)
    {
        $sx = '<a href="' . PATH . 'dados/dataverse/translate/export/' . $file . '" class="btn btn-outline-primary me-2 small mb-2">' . bsicone('download') . ' ' . lang('brapci.download') . '</a>';
        return $sx;
    }

    function save_translate($file,$l)
        {
            $sx = '';
            $ln = troca($l,chr(10),chr(13));
            while (strpos($ln,chr(13).chr(13)))
                {
                    $ln = troca($ln, chr(13) . chr(13), chr(13));
                }
            $l = explode(chr(13),$ln);
            $sx .= '<ul>';
            foreach($l as $idx=>$ln)
                {
                    $txt = trim(substr($ln,strpos($ln,' ')));
                    $idn = sonumero(substr($ln,0,strpos($ln,']')));
                    $dt = [];
                    $dt['dvn_pt'] = $txt;
                    $this->set($dt)
                        ->where('id_dvn',$idn)
                        ->update();
                    $sx .= '<li>'.$idn.' update'.'</li>'.cr();
                }
            $sx .= '</ul>';
            return $sx;
        }

    function mass($file)
    {
        $sx = '';

        $act = get("action");
        $rst = get("result");
        $sxr = '';

        if (($act != '') and ($rst != ''))
            {
                $sxr = $this->save_translate($file,$rst);
            }
        $dt = $this
            ->where('dvn_file', $file)
            ->where('dvn_pt', '')
            ->findAll();
        $txt = '';
        foreach($dt as $id=>$line)
            {
                $txt .= '['.$line['id_dvn'].'] ';
                $txt .= $line['dvn_en'];
                $txt .= cr();
            }
        $sx = form_open();
        $sx .= '<br>'.lang('brapci.source');
        $sx .= form_textarea('source',$txt,['disabled'=>'false','class'=>'mb-form-control full']);

        $sx .= '<br>' . lang('brapci.result');
        $sx .= form_textarea('result', '', ['enabled' => 'true', 'class' => 'mb-form-control full']);
        $sx .= '<br>';
        $sx .= '<br>';
        $sx .= form_submit('action',lang('brapci.save'),['class'=>'btn btn-primary']);
        $sx .= form_close();

        $sx .= $sxr;

        return $sx;
    }

    function view($file)
        {
            $sx = '';
            $dt = $this
                ->where('dvn_file',$file)
                ->findAll();
            $sx .= bsc(h($dt[0]['dvn_file']), 12);
            $sx .= bsc($this->btn_translate($file).$this->btn_export($file),12);
            $sx .= '<table class="form-control full">';
            $sx .= '<tr><th width="30%">label</th>
                        <th width="35%">English</th>
                        <th width="35%">Português</th>
                        </tr>';
            foreach($dt as $id=>$line)
                {
                    $sx .= '<tr>';
                    $sx .= '<td class="border-top border-secondary">';
                    $sx .= $line['dvn_field'];
                    $sx .= '</td>';
                    $sx .= '<td class="border-top border-secondary">';
                    $sx .= $line['dvn_en'];
                    $sx .= '</td>';
                    $sx .= '<td class="border-top border-secondary">';
                    $sx .= $line['dvn_pt'];
                    $sx .= '</td>';
                    $sx .= '<td>';
                    $sx .= '<tr>';
                }
            $sx .= '</table>';
            $sx = bs($sx);
            return $sx;
        }
    function files()
        {
            $sx = '';
            $dt = $this
                    ->select('dvn_file, count(*) as total')
                    ->groupby('dvn_file')
                    ->orderby('dvn_file')
                    ->findAll();
            $sx .= '<ul>';
            foreach($dt as $id=>$line)
                {
                    $link = '<a href="'.PATH.'/dados/dataverse/translate/file/'. $line['dvn_file'].'">';
                    $linka = '</a>';
                    $sx .= '<li>'.$link.$line['dvn_file'].$linka.' ('.$line['total'].')</li>';
                }
            $sx .= '</ul>';
            return $sx;
        }
}
