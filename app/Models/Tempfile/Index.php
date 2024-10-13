<?php

namespace App\Models\Tempfile;

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

    function upload($d1, $d2, $d3)
    {
        $RSP = [];
        if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
            // Obtém informações do arquivo
            $fileTmpPath = $_FILES['file']['tmp_name'];
            $fileName = $_FILES['file']['name'];
            $fileSize = $_FILES['file']['size'];
            $fileType = $_FILES['file']['type'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));

            // Define o diretório de destino
            $d1 = strtolower($d1);
            $d1 = troca($d1,' ','_');
            $uploadFileDir = '../.tmp/'.$d1;
            dircheck($uploadFileDir);
            $dest_path = $uploadFileDir . $fileName;

            // Verifica se o diretório de upload existe, caso contrário, cria o diretório
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0777, true);
            }

            // Move o arquivo do local temporário para o destino final
            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $RSP['message'] = 'Arquivo enviado com sucesso.';
                $RSP['status'] = '200';
                $RSP['file'] = $fileName;
                $RSP['dest'] = $dest_path;
            } else {
                $RSP['message'] = 'Houve um erro ao mover o arquivo para o diretório de upload.';
                $RSP['status'] = '500';
            }
        } else {
            $RSP['message'] = 'Nenhum arquivo enviado.';
            $RSP['status'] = '500';
        }
        return $RSP;
    }
}
