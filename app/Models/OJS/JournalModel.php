<?php

namespace App\Models\OJS;

use CodeIgniter\Model;
use RuntimeException;

class JournalModel extends Model
{
    protected $DBGroup = 'ojs_import';
    protected $table = 'ojs_journals';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useAutoIncrement = true;
    protected $useTimestamps = true;
    protected $validationRules = [
        'name' => 'required|max_length[200]',
        'acronym' => 'permit_empty|max_length[30]',
        'base_url' => 'required|valid_url_strict|max_length[255]',
        'context_path' => 'required|alpha_dash|max_length[100]',
        'api_key' => 'required',
        'api_key_editor' => 'required',
        'is_default' => 'permit_empty|in_list[0,1]',
        'active' => 'permit_empty|in_list[0,1]',
    ];
    protected $validationMessages = [
        'name' => ['required' => 'Informe o nome da revista.'],
        'base_url' => [
            'required' => 'Informe a URL base do OJS.',
            'valid_url_strict' => 'Informe uma URL válida, incluindo https://.',
        ],
        'context_path' => [
            'required' => 'Informe o caminho da revista no OJS.',
            'alpha_dash' => 'O caminho pode conter somente letras, números, hífen e sublinhado.',
        ],
        'api_key' => ['required' => 'Informe a APIKEY da revista.'],
        'api_key_editor' => ['required' => 'Informe a APIKEY editorial da revista.'],
    ];
    protected $allowedFields = [
        'name',
        'acronym',
        'base_url',
        'context_path',
        'api_key',
        'api_key_editor',
        'is_default',
        'active',
    ];

    public function getDefaultWithCredentials(): array
    {
        $journal = $this->where('active', 1)
            ->orderBy('is_default', 'DESC')
            ->orderBy('id', 'ASC')
            ->first();

        if ($journal === null) {
            throw new RuntimeException('Nenhuma revista OJS ativa foi configurada.');
        }

        return $journal;
    }

    public function getApiUrl(array $journal): string
    {
        return rtrim($journal['base_url'], '/')
            . '/index.php/'
            . trim($journal['context_path'], '/')
            . '/api/v1';
    }

    public function saveJournal(array $data): bool
    {
        $this->db->transStart();

        if (!empty($data['is_default'])) {
            $this->builder()->set('is_default', 0)->update();
        }

        $saved = $this->save($data);
        if (!$saved) {
            $this->db->transRollback();
            return false;
        }

        $this->db->transComplete();
        return $this->db->transStatus();
    }}