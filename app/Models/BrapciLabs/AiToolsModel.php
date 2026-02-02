<?php

namespace App\Models\BrapciLabs;

use CodeIgniter\Model;

class AiToolsModel extends Model
{
    protected $DBGroup          = 'brapci_labs';
    protected $table            = 'ai_tools';
    protected $primaryKey       = 'id_ai';
    protected $useAutoIncrement = true;

    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'ai_name',
        'ai_description',
        'ai_parameters',
        'ai_command',
        'ai_version',
        'ai_update',
        'ai_created',
        'ai_tags',
        'ai_language'
    ];

    // Datas
    protected $useTimestamps = true;
    protected $createdField  = 'ai_created';
    protected $updatedField  = '';        // não existe campo update automático
    protected $deletedField  = '';

    // Validações
    protected $validationRules = [
        'ai_name'        => 'required|min_length[3]|max_length[200]',
        'ai_version'     => 'required|max_length[10]',
        'ai_update'      => 'required|valid_date',
    ];

    protected $validationMessages = [
        'ai_name' => [
            'required' => 'O nome da ferramenta é obrigatório.'
        ],
        'ai_version' => [
            'required' => 'A versão é obrigatória.'
        ],
        'ai_update' => [
            'required' => 'A data de atualização é obrigatória.'
        ],
    ];

    protected $skipValidation = false;

    /**
     * Retorna ferramentas ordenadas pela data de atualização
     */
    public function getAllOrdered()
    {
        return $this->orderBy('ai_update', 'DESC')->findAll();
    }

    /**
     * Busca por nome (LIKE)
     */
    public function searchByName(string $term)
    {
        return $this->like('ai_name', $term)
            ->orderBy('ai_name', 'ASC')
            ->findAll();
    }

    function le($id = 0)
    {
        return $this->where('id_ai', $id)->first();
    }

    function view($id)
    {
        $data = $this->le($id);
        return view('BrapciLabs/ai/console', ['tool' => $data]);
    }
}
