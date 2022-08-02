<?php

namespace App\Models\Books;

use CodeIgniter\Model;

class TechinalProceessing extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'brapci_books.technical_processing';
    protected $primaryKey       = 'id_tp';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_tp', 'tp_checksun', 'tp_up',
        'tp_file', 'tp_user', 'tp_status',
        'tp_created', 'tp_ip'
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

    function upload($file, $tmp)
    {
        dircheck('.tmp/');
        dircheck('.tmp/books');
        $sx = '';

        /******************* Extension */
        $user = 0;
        $ext = explode('.', $file);
        $ext = strtolower($ext[count($ext) - 1]);

        $data['tp_checksun'] = md5_file($tmp);
        $data['tp_file'] = $file;
        $data['tp_user'] = $user;
        $data['tp_ip'] = ip();
        $data['tp_status'] = 0;
        $dest = '.tmp/books/' . $data['tp_checksun'] . '.' . $ext;
        $data['tp_up'] = $dest;

        /************************* File Description */
        $file_description = '<tt>' . $file . '</tt>';
        $file_description .= ' <tt>' .
            number_format(filesize($tmp) / 1024 / 1024, 1) . 'Mbyte</tt>';

        $dt = $this->where('tp_checksun', $data['tp_checksun'])->findAll();

        if (count($dt) == 0) {
            move_uploaded_file($tmp, $dest);
            $this->set($data)->insert();
            $sx .= bsmessage(lang('book.sucess_autodeosit') . ' - ' .
                lang('sucess_autodeosit_info') .
                '<br>' . $file_description, 1);
        } else {
            $sx .= bsmessage(lang('book.already_autodeosit') . ' - ' .
                lang('already_autodeosit_info') .
                '<br>' . $file_description, 3);
        }
        return $sx;
    }
}