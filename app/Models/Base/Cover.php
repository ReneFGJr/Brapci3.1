<?php

namespace App\Models\Base;

use CodeIgniter\Model;

class Cover extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'downloads';
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

    function tumb($id,$dt)
        {
            $RDF = new \App\Models\Rdf\RDF();
            if ((isset($dt['n_name2'])) and (trim($dt['n_name2']) != ''))
                {
                    $img = trim($dt['n_name2']);
                }
            $img = $RDF->c($id);
            $place = $img;
            if (substr($img,0,4) == 'http')
                {
                    $place = troca($img,PATH,'');
                    if (substr($place,0,1) == '/')
                        {
                            $place = substr($place,1,strlen($place));
                        }
                } else {
                    $place = $img;
                }

            if (!file_exists($place))
                {
                    $img = '/img/books/no_cover.png';
                }
            return $img;
        }

    function cover_upload_bnt($jnl=0)
        {
            $sx = '<span class="supersmall pointer" onclick="newwin(\''.PATH.'/admin/upload_cover/'.$jnl.'\',400,400);">';
            $sx .= lang('brapci.upload_cover');
            $sx .= '</span>';
            return $sx;
        }
    function cover_upload($jnl = 0)
    {
        $sx = '';
        $sx .= form_open_multipart();
        $sx .= form_upload('cover');
        $sx .= form_submit('action',lang('brapci.send'));
        $sx .= form_close();


        if (isset($_FILES['cover']['name']))
            {
                pre($_FILES, false);
                $tmp = $_FILES['cover']['tmp_name'];
                $type = $_FILES['cover']['type'];

                switch($type)
                    {
                        case 'image/png':
                            $ok = true;
                            $ext = '.png';
                            break;
                        case 'image/jpeg':
                            $ext = '.jpg';
                            $ok = true;
                            break;
                        default:
                            $ok = false;
                            $sx .= bsmessage('Formato inválido',3);
                    }

                if ($ok==true)
                    {
                        $dir = '_repository/cover/';
                        $dest = $dir. 'cover_issue_'. strzero(round($jnl),4).$ext;
                        dircheck($dir);

                        move_uploaded_file($tmp,$dest);

                        $sx = wclose();
                    }
            }

        return $sx;
    }

    function cover($jnl=0)
        {
            $img = '_repository/cover/cover_issue_'.strzero($jnl,4).'.jpg';
            echo $img;
            if (file_exists($img))
                {
                    return URL.'/'.$img;
                }
            exit;

            $img = '_repository/cover/cover_issue_' . strzero($jnl, 4) . '.png';
            if (file_exists($img)) {
                return URL . '/' . $img;
            }

            $img = 'img/cover/cover_issue_' . strzero($jnl, 4) . '.png';
            if (file_exists($img)) {
                return URL . '/' . $img;
            }

            $img = 'img/cover/cover_issue_' . strzero($jnl, 4) . '.jpg';
            if (file_exists($img)) {
                return URL . '/' . $img;
            }

            $img = 'img/thema/no_cover.png';
            return PATH . '/' . $img;
        }

    function book($id = '')
    {
        $RDF = new \App\Models\RDF2\RDF();
        $RDFdata = new \App\Models\RDF2\RDFdata();
        $dt = $RDF->le($id);

        /************* Recupera o Livro */
        $img = trim($RDF->extract($dt, 'hasCover', 'F'));
        if ($img == '') {
            $img = 'img/books/no_cover.png';
        }
        return $img;
    }

    function bookChapter($id = '')
    {
        $RDF = new \App\Models\RDF2\RDF();
        $RDFdata = new \App\Models\RDF2\RDFdata();
        $dt = $RDF->le($id);

        /************* Recupera o Livro */
        $cover = $RDF->extract($dt, 'hasBookChapter','A');
        $img = '';
        if (isset($cover[0]))
            {
                /************* Metadados do Livro */
                $dt = $RDF->le($cover[0]);
                $img = trim($RDF->extract($dt, 'hasCover', 'F'));
            }
        if ($img=='')
            {
                $img = 'img/books/no_cover.png';
            }
        return $img;
    }


    function image($id='')
    {
        $RDF = new \App\Models\Rdf\RDF();
        $img = $RDF->c($id);
        $img_chk = troca($img,URL,'');
        $img_chk = substr($img_chk,1,strlen($img_chk));
        if (!file_exists($img_chk))
            {
                $img = '/img/thema/image_broke.svg';
            }
        return $img;
    }
}