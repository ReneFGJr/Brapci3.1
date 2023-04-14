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
                    default:
                        $sx .= $this->actions();
                        $sx .= $this->files();
                        break;
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

    function view($file)
        {
            $sx = '';
            $dt = $this
                ->where('dvn_file',$file)
                ->findAll();
            $sx .= bsc(h($dt[0]['dvn_file']), 12);
            foreach($dt as $id=>$line)
                {
                    $class = 'border-bottom border-secondaty';
                    $sx .= bsc($line['dvn_field'], 4, 'supersmall '.$class);
                    $sx .= bsc($line['dvn_en'],4, 'small text-primary '.$class);
                    $sx .= bsc($line['dvn_pt'],4, 'small text-success '.$class);
                }
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
