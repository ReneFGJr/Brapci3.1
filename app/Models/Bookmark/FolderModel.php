<?php

namespace App\Models\Bookmark;

use CodeIgniter\Model;

class FolderModel extends Model
{
    protected $DBGroup          = 'bookmarks';
    protected $table            = 'folder';
    protected $primaryKey       = 'id_f';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'f_title',
        'f_folder',
        'f_access',
        'f_user',
        'f_description',
        'created_at'
    ];

    // timestamps
    protected $useTimestamps = false;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';
    protected $deletedField  = '';

    // Validation rules (opcional)
    protected $validationRules = [
        'f_title'  => 'required|min_length[2]|max_length[255]',
        'f_folder' => 'permit_empty|max_length[255]',
        'f_access' => 'is_natural',
        'f_user'   => 'is_natural_no_zero'
    ];

    protected $validationMessages = [
        'f_title' => [
            'required' => 'O título da pasta é obrigatório.'
        ]
    ];

    protected $skipValidation = false;

    // 🔹 Função utilitária: retorna todas as pastas de um usuário
    public function getByUser(int $user_id)
    {
        return $this->where('f_user', $user_id)
            ->orderBy('f_title', 'ASC')
            ->findAll();
    }

    public function  convert()
    {
        $BookmarkModel = new BookmarkModel();
        $bookmarks = $BookmarkModel->findAll();

        foreach ($bookmarks as $b) {
            $folder_name = trim($b['folder']);
            if (empty($folder_name)) {
                continue;
            }
            $folder = $this->where('f_folder', $folder_name)->first();
            if (!$folder) {
                echo "OPS: " . $folder_name . "<br>";
                $dd = [
                    'f_title'  => $folder_name,
                    'f_folder' => $folder_name,
                    'f_access' => 0,
                    'f_user'   => 1,
                    'created_at' => date('Y-m-d H:i:s')
                ];
                $this->insert($dd);
                $folder_id = $this->getInsertID();
            } else {
                $folder_id = $folder['id_f'];
            }
            // atualizar o bookmark
            $dd = [
                'folder_id' => $folder_id
            ];
            $BookmarkModel->set($dd)->where('id', $b['id'])->update();
        }
    }
}
