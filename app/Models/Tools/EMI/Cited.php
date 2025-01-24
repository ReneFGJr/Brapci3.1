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
                $text = 'VANZ, Samile  Andrea de Souza; SANTIN, Dirce Maria.; PAVÃO, Caterina Marta Groposo. A bibliometria e as novas atribuições profissionais nas bibliotecas universitárias. InCID: Revista de Ciência da Informação e Documentação, [S. l.], v. 9, n. 1, p. 4-24, 2018. DOI: 10.11606/issn.2178-2075.v9i1p4-24. Disponível em: https://www.revistas.usp.br/incid/article/view/137741.  Acesso em: 17 set. 2022. ';
                $anoAtual = (int)date("Y");
                $RSP = $this->halflive($text,$anoAtual);
                echo json_encode($RSP);
                exit;
                break;
        }
        return $sx;
    }

    function extrairAnoPublicacao(string $texto): ?int
    {
        /**
         * Extrai o ano de publicação de um texto.
         *
         * Parâmetros:
         *     texto (string): Texto que contém a referência bibliográfica.
         *
         * Retorno:
         *     int|null: O ano de publicação ou null se não encontrado.
         */

        // Expressão regular para capturar um ano (quatro dígitos)
        $pattern = '/\\b(\\d{4})\\b/';
        preg_match_all($pattern, $texto, $matches);

        if (!empty($matches[1])) {
            // Iterar pelos anos encontrados para identificar o mais provável
            foreach ($matches[1] as $ano) {
                $ano = (int)$ano;
                if ($ano >= 1500 && $ano <= (int)date("Y")) {
                    // Retorna o primeiro ano que esteja dentro de um intervalo plausível
                    return $ano;
                }
            }
        }

        // Retorna null se nenhum ano válido for encontrado
        return null;
    }

    function halflive($text, $anoAtual): ?float
    {
        $RSP = [];
        if ($text == '')
            {
            $RSP['status'] = '500';
            $RSP['message'] = 'Não existe referências para processar';
            return $RSP;
            }

        $publicacoes = explode("\n", $text);
        $anos = [];
        $anoAtual = (int)date("Y");


        foreach ($publicacoes as $publicacao) {
                $ano = $this->extrairAnoPublicacao($publicacao);
                $anos[] = $ano;
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
        $RSP['works'] = $publicacoes;
        $RSP['years'] = $anos;
        $RSP['halflive'] = $anoAtual - $meiaVida;
        pre($RSP);
        return $RSP;
    }
}
