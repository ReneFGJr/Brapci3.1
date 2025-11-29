<?php

namespace App\Controllers;

use App\Models\Bookmark\BookmarkModel;
use App\Models\Bookmark\FolderModel;
use Google\Service\CloudResourceManager\Folder;

helper(['boostrap', 'url', 'sisdoc_forms', 'form', 'nbr', 'sessions', 'cookie']);
$session = \Config\Services::session();

class Bookmarks extends BaseController
{
    public function index()
    {
        $model = new BookmarkModel();
        $data['bookmarks'] =
            $model
            ->join('folder', 'folder.id_f = bookmarks.folder_id', 'left')
            ->orderBy('folder.f_title', 'ASC')
            ->findAll();
        return view('bookmarks/index', $data);
    }

    function folder()
    {
        $model = new FolderModel();
        $data['folders'] = $model
            ->orderBy('f_click', 'DESC')
            ->orderBy('f_title', 'ASC')
            ->findAll();
        return view('bookmarks/folder/index', $data);
    }

    public function folderNew()
    {
        return view('bookmarks/folder/new');
    }

    public function folderSave()
    {
        $model = new FolderModel();

        $title = trim($this->request->getPost('f_title'));
        $description = trim($this->request->getPost('f_description'));

        // 🔍 Verifica se já existe uma pasta com o mesmo nome (case-insensitive)
        $exists = $model->where('LOWER(f_title)', strtolower($title))->first();

        if ($exists) {
            // Pasta já existe → retorna com mensagem de erro
            return redirect()->back()
                ->withInput()
                ->with('error', 'Já existe uma pasta com este nome!');
        }

        // 🗂️ Caso não exista, insere nova pasta
        $data = [
            'f_title' => $title,
            'f_description' => $description,
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $model->insert($data);

        return redirect()->to(base_url('bookmarks/folder'))
            ->with('success', 'Pasta criada com sucesso!');
    }

    public function siteDelete($id)
    {
        $model = new BookmarkModel();

        // Busca o registro
        $site = $model->find($id);

        if (!$site) {
            return redirect()->back()->with('error', 'Site não encontrado.');
        }

        // Atualiza o campo active = 0
        $model->update($id, [
            'active' => 0,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        return redirect()->to(base_url('bookmarks/folders/view/' . $site['folder_id']))
            ->with('success', 'Site removido com sucesso!');
    }


    function link($id)
    {
        $model = new BookmarkModel();
        $data = $model->find($id);
        $count = $data['clicks'] + 1;
        $model->update($id, ['clicks' => $count, 'click_last' => date('Y-m-d H:i:s')]);

        return redirect()->to($data['url']);
    }

    function folderView($id)
    {
        $model = new BookmarkModel();
        $data['bookmarks'] =
            $model
            ->where('folder_id', $id)
            ->where('active', 1)
            ->join('folder', 'folder.id_f = bookmarks.folder_id', 'left')
            ->orderBy('clicks', 'DESC')
            ->orderBy('title', 'ASC')
            ->findAll();

        $folderModel = new FolderModel();;
        $data['folder'] = $folderModel->find($id);

        return view('bookmarks/folder/view', $data);
    }

    public function import()
    {
        $Folder = new FolderModel();;
        $Folder->convert();
        exit;

        helper('filesystem');
        $file = WRITEPATH . 'uploads/Bookmarks'; // copie o arquivo para esta pasta

        $json = file_get_contents($file);
        $data = json_decode($json, true);

        $model = new BookmarkModel();
        $this->importNodes($data['roots'], $model);

        return redirect()->to('/bookmarks');
    }

    private function importNodes(array $nodes, BookmarkModel $model, string $folder = '')
    {
        foreach ($nodes as $node) {
            if (isset($node['children'])) {
                $this->importNodes($node['children'], $model, $node['name']);
            } elseif (isset($node['url'])) {
                $model->insert([
                    'title' => $node['name'],
                    'url' => $node['url'],
                    'folder' => $folder,
                    'date_added' => date('Y-m-d H:i:s', $node['date_added'] / 1000000),
                    'favicon' => 'https://www.google.com/s2/favicons?sz=32&domain_url=' . parse_url($node['url'], PHP_URL_HOST)
                ]);
            }
        }
    }

    public function search()
    {
        $query = $this->request->getGet('q');
        $model = new BookmarkModel();
        $data['bookmarks'] = $model
            ->like('title', $query)
            ->orLike('url', $query)
            ->orderBy('title', 'ASC')
            ->findAll();

        return view('bookmarks/index', $data);
    }

    /**
     * Exibe formulário para adicionar um novo site dentro de uma pasta
     */
    public function siteNew($folder_id)
    {
        $folderModel = new FolderModel();
        $data['folder'] = $folderModel->find($folder_id);

        if (!$data['folder']) {
            return redirect()->to(base_url('bookmarks/folder'))
                ->with('error', 'Pasta não encontrada.');
        }

        return view('bookmarks/site/new', $data);
    }

    /**
     * Salva o novo site no banco de dados
     */
    public function siteSave()
    {
        $model = new BookmarkModel();

        $folder_id = $this->request->getPost('folder_id');
        $title = trim($this->request->getPost('title'));
        $url = trim($this->request->getPost('url'));
        $description = trim($this->request->getPost('description'));

        // 🔍 Verifica duplicado
        $exists = $model->where('folder_id', $folder_id)
            ->where('LOWER(url)', strtolower($url))
            ->first();

        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Este site já foi adicionado nesta pasta.');
        }

        $data = [
            'title' => $title,
            'url' => $url,
            'description' => $description,
            'folder_id' => $folder_id,
            'date_added' => date('Y-m-d H:i:s'),
            'clicks' => 0,
            'favicon' => 'https://www.google.com/s2/favicons?sz=32&domain_url=' . parse_url($url, PHP_URL_HOST)
        ];

        $model->insert($data);

        return redirect()->to(base_url('bookmarks/folders/view/' . $folder_id))
            ->with('success', 'Site adicionado com sucesso!');
    }
}
