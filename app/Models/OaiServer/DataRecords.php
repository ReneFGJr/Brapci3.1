<?php

namespace App\Models\OaiServer;

use CodeIgniter\Model;

class DataRecords extends Model
{
    protected $DBGroup          = 'oaiserver';
    protected $table            = 'Records_data';
    protected $primaryKey       = 'id_r ';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'r_record',
        'r_metadata',
        'r_lang',
        'r_content'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

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

    function le($id)
        {
            $dt = $this
                ->where('id_r',$id)
                ->first($id);
            return $dt;
        }

    function remove($id)
    {
        // Validação do ID
        if (!is_numeric($id) || $id <= 0) {
            return ['status' => '400', 'message' => 'Invalid ID', 'ID'=> $id];
        }

        try {
            // Verifica se o registro existe
            $exists = $this->where('id_r', $id)->exists();
            if (!$exists) {
                return ['status' => '404', 'message' => 'Record not found'];
            }

            // Remove o registro
            $this->where('id_r', $id)->delete();

            // Retorna sucesso
            return ['status' => '200', 'message' => 'Record deleted successfully'];
        } catch (\Exception $e) {
            // Retorna erro em caso de falha
            return ['status' => '500', 'message' => 'Error deleting record: ' . $e->getMessage()];
        }
    }


    function register($id, $dt)
    {
        if ($dt['r_metadata'] == '4') {
            $name = $dt['r_content'];
            $nameArray = explode(';', $name); // Divide a string em um array usando ';' como delimitador

            foreach ($nameArray as $nameX) {
                $nameX = troca($nameX,'.',' ');
                $dt['r_content'] = trim($nameX); // Atualiza 'r_content' para cada item do array
                $dt['xxx'] = $nameArray;
                $this->registerSub($id, $dt); // Chama a função 'registerSub' para cada item
            }
        } else {
            $dt['xxx'] = $dt['r_metadata'];
            $this->registerSub($id, $dt); // Processa diretamente se 'r_metadata' não for 4
        }
        return $dt;
    }

    function registerSub($id,$dt)
            {
            if ($id == 0)
                {
                    $this->set($dt)->where('id_r', $dt['id_r'])->insert();
                } else {
                    $this->set($dt)->where('id_r', $dt['id_r'])->update();
                }
            return $dt;
        }

    function list($id)
        {
            $dt =
                $this
                ->join('brapci_oaipmh_editor.metadata', 'r_metadata = id_mt')
                ->where('r_record',$id)
                ->findAll();
            return $dt;
        }
}
