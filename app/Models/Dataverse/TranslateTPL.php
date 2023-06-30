<?php

namespace App\Models\Dataverse;

use CodeIgniter\Model;

class TranslateTPL extends Model
{
    protected $DBGroup          = 'dataverse';
    protected $table            = 'translate_tpl';
    protected $primaryKey       = 'id_tpl';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_tpl', 'tpl_file', 'tpl_t1', 'tpl_t2', 'tpl_en', 'tpl_br'
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
            dircheck($dir);
            $file = $dir.$fn;
            echo $file;
            $tx = '';
            if (file_exists($file))
                {
                    $dt = $this
                        ->where('tpl_file', $fn)
                        ->findAll();

                    $dd = [];
                    foreach($dt as $id=>$line)
                        {
                            $field = $line['tpl_en'];
                            $dd[$field] = $line['tpl_br'];
                        }
                    $txt = file_get_contents($file);
                    $ln = explode(chr(10),$txt);

                    foreach($ln as $id=>$l)
                        {
                            $c = substr($l,0,1);
                            switch($c)
                                {
                                    case '#':
                                        $tx .= $l.chr(10);
                                        break;
                                    default:
                                        if (($pos=strpos($l,' "')) > 0)
                                            {
                                                $lb = trim(substr($l,$pos,strlen($l)));
                                                $lb = troca($lb,' .','');
                                                $lb = troca($lb,'"','');

                                                if (isset($dd[$lb]))
                                                    {
                                                        $tx .= troca($l,$lb,$dd[$lb]).chr(10);
                                                    } else {
                                                        echo "ERRO";
                                                        echo '<br>'.$l;
                                                        $tx .= $l . chr(10);
                                                    }
                                            } else {
                                                $tx .= $l.chr(10);
                                            }
                                        break;
                                }
                        }
                    $fileD = troca($file,'/translate/','/translate/_');
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

    function register($file,$field,$prop,$en,$pt='')
        {
                $dt = $this
                    ->where('tpl_file', $file)
                    ->where('tpl_t1', $field)
                    ->where('tpl_en', $en)
                    ->first();
                if ($dt == '')
                    {
                        $data['tpl_file'] = $file;
                        $data['tpl_t1'] = $field;
                        $data['tpl_t2'] = $prop;
                        $data['tpl_en'] = $en;
                        if ($pt != '')
                            {
                                $data['tpl_br'] = $pt;
                            } else {
                                $data['tpl_br'] = '';
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
                            $line = troca($line,'> <','];[');
                            $line = troca($line, '> "', '];');
                            $line = troca($line, '<', '[');
                            $line = troca($line,'" .','');
                            $field = explode(';',$line);
                            if (count($field) > 0)
                            {
                                $sx .= '<li>'.$field[0].' ';
                                $sx .= $this->register($file,$field[0],$field[1],$field[2],'');
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
            $sx .= anchor(PATH. '/dados/dataverse/translateTPL/upload',bsicone('upload',32));
            $sx = bs(bsc($sx));
            return $sx;
        }

    function btn_translate($file)
        {
            $sx = '<a href="'.PATH. 'dados/dataverse/translateTPL/mass/'.$file.'" class="btn btn-outline-primary me-2 small mb-2">'.bsicone('import').' '.lang('brapci.translate').'</a>';
            return $sx;
        }

    function btn_export($file)
    {
        $sx = '<a href="' . PATH . 'dados/dataverse/translateTPL/export/' . $file . '" class="btn btn-outline-primary me-2 small mb-2">' . bsicone('download') . ' ' . lang('brapci.download') . '</a>';
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
                    $dt['tpl_br'] = $txt;
                    $this->set($dt)
                        ->where('id_tpl',$idn)
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
            ->where('tpl_file', $file)
            ->where('tpl_br', '')
            ->findAll();
        $txt = '';
        foreach($dt as $id=>$line)
            {
                $txt .= '['.$line['id_tpl'].'] ';
                $txt .= $line['tpl_en'];
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
                ->where('tpl_file',$file)
                ->findAll();
            $sx .= bsc(h($dt[0]['tpl_file']), 12);
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
                    $sx .= $line['tpl_t1'];
                    $sx .= '</td>';
                    $sx .= '<td class="border-top border-secondary">';
                    $sx .= $line['tpl_en'];
                    $sx .= '</td>';
                    $sx .= '<td class="border-top border-secondary">';
                    $sx .= $line['tpl_br'];
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
                    ->select('tpl_file, count(*) as total')
                    ->groupby('tpl_file')
                    ->orderby('tpl_file')
                    ->findAll();
            $sx .= '<ul>';
            foreach($dt as $id=>$line)
                {
                    $link = '<a href="'.PATH.'/dados/dataverse/translateTPL/file/'. $line['tpl_file'].'">';
                    $linka = '</a>';
                    $sx .= '<li>'.$link.$line['tpl_file'].$linka.' ('.$line['total'].')</li>';
                }
            $sx .= '</ul>';
            return $sx;
        }
}
