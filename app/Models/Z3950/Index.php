<?php
namespace App\Models\Z3950;

use CodeIgniter\Model;

class Index extends Model
{
    public array $trace = [];

    private function phase(string $phase, string $detail): void
    {
        $this->trace[] = ['phase' => $phase, 'detail' => $detail, 'time' => microtime(true)];
    }

    public function searchByISBN(string $isbn)
    {
        $this->trace = [];
        $this->phase('validation', 'Validando ISBN e disponibilidade PHP-YAZ');
        $isbn = \App\Libraries\Z3950Isbn::normalize($isbn);
        if ($isbn === '') throw new \InvalidArgumentException('ISBN inválido.');
        if (!function_exists('yaz_connect')) throw new \RuntimeException('Extensão PHP-YAZ indisponível.');
        $server = (string) env('metadata.z3950Server', 'z3950.bn.br:210/biblioteca');
        $this->phase('connection', 'Inicializando conexão Z39.50: ' . $server);
        $id = yaz_connect($server);
        if (!$id) throw new \RuntimeException('Não foi possível conectar ao servidor Z39.50.');
        try {
            $this->phase('configuration', 'Sintaxe usmarc, primeiro registro');
            yaz_syntax($id, 'usmarc');
            yaz_range($id, 1, 1);
            $this->phase('search', 'Preparando busca RPN: @attr 1=7 ' . $isbn);
            yaz_search($id, 'rpn', '@attr 1=7 ' . $isbn);
            $this->phase('negotiation', 'Executando conexão, negociação e busca via yaz_wait (timeout 30s)');
            $options = ['timeout' => 30];
            yaz_wait($options);
            if (yaz_errno($id)) throw new \RuntimeException('Z39.50: ' . yaz_error($id));
            $hits = yaz_hits($id);
            $this->phase('results', 'Registros encontrados: ' . $hits);
            if ($hits === 0) return null;
            $this->phase('record', 'Lendo registro MARCXML');
            $record = yaz_record($id, 1, 'xml');
            if (!$record) throw new \RuntimeException('O servidor não retornou o registro MARCXML.');
            $this->phase('record_received', 'MARCXML recebido: ' . strlen($record) . ' bytes');
            return $record;
        } catch (\Throwable $e) {
            $this->phase('error', $e->getMessage());
            throw $e;
        } finally {
            yaz_close($id);
            $this->phase('close', 'Conexão Z39.50 encerrada');
        }
    }
}
