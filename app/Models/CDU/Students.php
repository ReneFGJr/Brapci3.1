<?php

namespace App\Models\CDU;

use CodeIgniter\Model;

class Students extends Model
{
    protected $DBGroup          = 'CDU';
    protected $table            = 'students';
    protected $primaryKey       = 'id_us';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_us',
        'us_cracha',
        'us_nome',
        'us_lastAccess',
        'us_created',
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

    function import($txt)
    {
        $txt = str_replace('"', '', $txt);
        $txt = str_replace(chr(13), chr(10), $txt);
        $line = explode(chr(10), $txt);
        foreach ($line as $l) {
            $l = trim($l);
            if (strlen($l) > 0) {
                $dados = explode("\t", $l);
                if (isset($dados[1])) {
                    $type = trim($dados[1]);

                    if ($type == 'GRAD') {

                        $cracha = strzero($dados[2], 8);
                        $dt = $this->where('us_cracha', $cracha)->first();
                        if ($dt == [])
                        {
                            $nome = nbr_author($dados[3],7);
                            $this->insert([
                                'us_cracha' => $cracha,
                                'us_nome' => $nome,
                            ]);
                        }
                    }
                }
            }
        }
        return $this->db->affectedRows();
    }

    function getStudents($id = 0) {}
}
