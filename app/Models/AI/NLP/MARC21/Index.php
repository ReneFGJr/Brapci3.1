<?php

namespace App\Models\AI\NLP\MARC21;

use CodeIgniter\Model;

class Index extends Model
{
    protected $DBGroup          = 'brapci_v3';
    protected $table            = 'vocabulary';
    protected $primaryKey       = 'id_vc';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_vc', 'vc_term', 'vc_pref',
        'vc_ID', 'vc_type', 'vc_size'
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

    function gerarMARC21($texto)
    {
        $linhas = explode("\n", $texto);
        $marc = [];

        $titulo = '';
        $autores = '';
        $resumo = '';
        $palavras_chave = '';
        $idioma = '';

        foreach ($linhas as $linha) {
            // Processa título
            if (strpos($linha, '|') !== false) {
                list($titulo, $pagina) = explode('|', $linha);
                $titulo = trim($titulo);
                $marc[] = "245 \$a $titulo";
            }

            // Processa autores
            if (strpos($linha, 'Resumo:') === false && !empty(trim($linha)) && empty($resumo)) {
                $autores .= trim($linha) . ', ';
            }

            // Processa resumo
            if (strpos($linha, 'Resumo:') !== false) {
                $resumo = trim(str_replace('Resumo:', '', $linha));
                $marc[] = "900 \$a $resumo";
            }

            // Processa palavras-chave
            if (strpos($linha, 'Palavras-chave:') !== false || strpos($linha, 'Palabras Clave:') !== false) {
                $palavras_chave = trim(str_replace(['Palavras-chave:', 'Palabras Clave:'], '', $linha));
                $palavras_chave = preg_replace('/\s*[,;]\s*/', '; ', $palavras_chave);
                $marc[] = "650 \$a $palavras_chave";
            }

            // Identifica o idioma
            if (strpos($linha, 'Palabras Clave:') !== false || strpos($linha, 'Resumen:') !== false) {
                $idioma = 'spa';  // espanhol
            } elseif (strpos($linha, 'Palavras-chave:') !== false || strpos($linha, 'Resumo:') !== false) {
                $idioma = 'por';  // português
            }
        }

        // Adiciona campo 100 (Autor)
        if (!empty($autores)) {
            $autores = rtrim($autores, ', ');
            $marc[] = "100 \$a $autores";
        }

        // Adiciona campo 901 (Idioma)
        if (!empty($idioma)) {
            $marc[] = "901 \$a $idioma";
        }

        return implode("\n", $marc);
    }
}
