<?php

namespace App\Controllers;

use App\Models\BookmarkModel;

helper(['boostrap', 'url', 'sisdoc_forms', 'form', 'nbr', 'sessions', 'cookie']);
$session = \Config\Services::session();

class Bookmarks extends BaseController
{
    public function index()
    {
        $model = new BookmarkModel();
        $data['bookmarks'] = $model->findAll();
        return view('bookmarks/index', $data);
    }

    public function import()
    {
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
