<?php

namespace App\Models;

use CodeIgniter\Model;

class IdentifyModel extends Model
{
    protected $DBGroup = 'oai_server';
    protected $table = 'identify';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'repository_name',
        'base_url',
        'protocol_version',
        'admin_email',
        'earliest_datestamp',
        'deleted_record',
        'granularity',
        'compression',
        'description'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'repository_name' => 'required|string|max_length[255]',
        'base_url' => 'required|string|max_length[500]',
        'protocol_version' => 'string|max_length[10]',
        'admin_email' => 'valid_email|max_length[255]',
        'earliest_datestamp' => 'required',
        'deleted_record' => 'in_list[no,persistent,transient]',
        'granularity' => 'string|max_length[50]',
    ];

    protected $validationMessages = [
        'repository_name' => [
            'required' => 'Nome do repositório é obrigatório',
            'max_length' => 'Nome do repositório não pode exceder 255 caracteres'
        ],
        'base_url' => [
            'required' => 'URL base é obrigatória',
            'max_length' => 'URL base não pode exceder 500 caracteres'
        ],
        'admin_email' => [
            'valid_email' => 'Email do administrador inválido'
        ]
    ];

    /**
     * Obter todos os registros de identificação
     */
    public function getAll()
    {
        return $this->findAll();
    }

    /**
     * Obter primeiro registro (geralmente há apenas um)
     */
    public function getFirst()
    {
        return $this->first();
    }

    /**
     * Obter por ID
     */
    public function getById($id)
    {
        return $this->find($id);
    }

    /**
     * Obter por nome do repositório
     */
    public function getByRepositoryName($name)
    {
        return $this->where('repository_name', $name)->first();
    }

    /**
     * Obter por path (identificador do repositório)
     */
    public function getByPath($path)
    {
        return $this->where('patch', $path)->first();
    }
}
