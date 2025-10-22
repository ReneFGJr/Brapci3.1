<?php

namespace App\Controllers;

use App\Models\BookmarkModel;
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
                ->findAll();
        return view('bookmarks/index', $data);
    }

    function folder()
    {
        $model = new \App\Models\FolderModel();
        $data['folders'] = $model->orderBy('f_title', 'ASC')->findAll();
        return view('bookmarks/folder/index', $data);
    }

    function link($id)
    {
        $model = new \App\Models\BookmarkModel();
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
                ->join('folder', 'folder.id_f = bookmarks.folder_id', 'left')
                ->findAll();

        $folderModel = new \App\Models\FolderModel();
        $data['folder'] = $folderModel->find($id);

        return view('bookmarks/folder/view', $data);
    }

    public function import()
    {
        $Folder = new \App\Models\FolderModel();
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
            ->findAll();

        return view('bookmarks/index', $data);
    }
}
