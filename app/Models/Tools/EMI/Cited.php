<?php

namespace App\Models\Tools\EMI;

use CodeIgniter\Model;

use function RectorPrefix20220609\dump_node;

class Cited extends Model
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

    function index($d1, $d2, $d3, $d4)
    {
        $sx = '';
        switch($d2)
        {
            case 'halflive':
                $RSP = $this->halflive($d1, $d2, $d3, $d4);
                echo json_encode($RSP);
                exit;
                break;
        }
        return $sx;
    }

    function halflive($text): ?float
    {
        /**
         * Calcula a meia-vida da literatura com base no ano de publicação.
         *
         * Parâmetros:
         *     publicacoes (array): Lista de strings contendo as publicações com título e ano.
         *
         * Retorno:
         *     float|null: Meia-vida calculada em anos ou null se não houver dados válidos.
         */
        $publicacoes = explode("\n", $text);
        $anos = [];
        $anoAtual = (int)date("Y");

        foreach ($publicacoes as $publicacao) {
            try {
                // Separar o título e o ano da publicação
                $partes = explode(",", $publicacao);
                if (count($partes) < 2) {
                    throw new Exception("Formato inválido na publicação: $publicacao");
                }

                $titulo = trim($partes[0]);
                $ano = trim(end($partes));

                // Extrair o ano se for válido
                if (ctype_digit($ano)) {
                    $anos[] = (int)$ano;
                } else {
                    echo "Ano inválido na publicação: $publicacao\n";
                }
            } catch (Exception $e) {
                echo $e->getMessage() . "\n";
            }
        }

        if (empty($anos)) {
            echo "Nenhum ano válido encontrado para calcular a meia-vida.\n";
            return null;
        }

        sort($anos);
        $totalAnos = count($anos);
        $medianIndex = (int)floor($totalAnos / 2);

        if ($totalAnos % 2 === 0) {
            $meiaVida = ($anos[$medianIndex - 1] + $anos[$medianIndex]) / 2;
        } else {
            $meiaVida = $anos[$medianIndex];
        }
        $RSP = [];
        $RSP['works'] = $publicacoes;
        $RSP['halflive'] = $anoAtual - $meiaVida;
        return $RSP;
    }
}
