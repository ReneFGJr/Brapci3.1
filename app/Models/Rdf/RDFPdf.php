<?php

namespace App\Models\Rdf;

use CodeIgniter\Model;

class RDFPdf extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'rdfpdfs';
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

    function view_file($id)
        {
            $Download = new \App\Models\Base\Download();
            $Download->download_pdf($id);
            exit;
        }

    function upload($id)
        {
            $RDF = new \App\Models\Rdf\RDF();
            $dt = $RDF->le($id);

            $files = $RDF->extract($dt, 'hasFileStorage');

            $sx = '';
            $sx .= form_open_multipart();
            $sx.= form_upload('file');
            $sx .= form_submit('action',lang('brapci.send'));
            $sx .= form_close();

            if (count($files) > 0)
                {
                    $sx .= bsmessage("Já existe um arquivo",3);
                    $new = false;
                } else {
                    $new = true;
                }

            if (isset($_FILES['file']['name']))
                {
                    $name = $_FILES['file']['name'];
                    $type = $_FILES['file']['type'];
                    $temp = $_FILES['file']['tmp_name'];
                    $error = $_FILES['file']['error'];
                    if ($error == 0)
                        {
                            if (!$new)
                                {
                                    $df = $RDF->le($files[0]);
                                    $dest = $df['concept']['n_name'];
                                    $dir = '';
                                    $xdir = explode('/',$dest);
                                    for ($r=0;$r < (count($xdir)-1);$r++)
                                        {
                                            $dir .= $xdir[$r].'/';
                                            dircheck($dir);
                                            echo $dir.'<br>';
                                        }

                                    if (file_exists($dest))
                                        {
                                            unlink($dest);
                                        }

                                    move_uploaded_file($temp,$dest);
                                    return wclose();
                                } else {
                                    echo "NOT IMPLEMENTED 001";
                                    exit;
                                }
                        }
                }
            return $sx;
        }
}
