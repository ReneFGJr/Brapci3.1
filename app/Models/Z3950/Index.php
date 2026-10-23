<?php
namespace App\Models\Z3950;

use CodeIgniter\Model;

class Index extends Model
{
    public function searchByISBN(string $isbn)
    {
        $isbn = \App\Libraries\Z3950Isbn::normalize($isbn);
        if ($isbn === '') throw new \InvalidArgumentException('ISBN inválido.');
        if (!function_exists('yaz_connect')) throw new \RuntimeException('Extensão PHP-YAZ indisponível.');
        $server = (string) env('metadata.z3950Server', 'z3950.bn.br:210/biblioteca');
        $id = yaz_connect($server);
        if (!$id) throw new \RuntimeException('Não foi possível conectar ao servidor Z39.50.');
        try {
            yaz_syntax($id, 'usmarc');
            yaz_range($id, 1, 1);
            yaz_search($id, 'rpn', '@attr 1=7 ' . $isbn);
            yaz_wait(['timeout' => 30]);
            if (yaz_errno($id)) throw new \RuntimeException('Z39.50: ' . yaz_error($id));
            if (yaz_hits($id) === 0) return null;
            $record = yaz_record($id, 1, 'xml');
            if (!$record) throw new \RuntimeException('O servidor não retornou o registro MARCXML.');
            return $record;
        } finally {
            yaz_close($id);
        }
    }
}
