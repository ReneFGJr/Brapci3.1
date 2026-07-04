<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\NotepadModel;

/* SESSION */
$language = \Config\Services::language();

helper(['boostrap', 'url', 'sisdoc_forms', 'form', 'nbr', 'sessions', 'cookie']);
$session = \Config\Services::session();

if (!defined('URL')) {
    define('URL', getenv('app.baseURL'));
}
if (!defined('PATH')) {
    define('PATH', getenv('app.baseURL') . getenv('app.baseURL.prefix'));
}
if (!defined('MODULE')) {
    define('MODULE', '');
}
if (!defined('PREFIX')) {
    define('PREFIX', '');
}
if (!defined('LIBRARY')) {
    define('LIBRARY', '0000');
}
if (!defined('COLLECTION')) {
    define('COLLECTION', '/notepad');
}

class Notepad extends BaseController
{
    private NotepadModel $notepad;

    public function __construct()
    {
        $this->notepad = new NotepadModel();
    }

    public function index()
    {
        $data = [
            'page_title' => 'Notepad',
            'bg' => 'bg-admin',
        ];

        $sx = '';
        $sx .= view('Brapci/Headers/header', $data);
        $sx .= view('Brapci/Headers/navbar', $data);
        $sx .= view('Notepad/home', $data);
        $sx .= view('Brapci/Headers/footer', $data);

        return $sx;
    }

    public function create()
    {
        $slug = (string) $this->request->getPost('slug');
        $slug = $this->notepad->sanitizeSlug($slug);

        if ($slug === '') {
            return redirect()->to('/notepad')->with('error', 'Informe um nome valido para a pagina.');
        }

        return redirect()->to('/notepad/' . $slug);
    }

    public function pad(string $slug = '')
    {
        $slug = $this->notepad->sanitizeSlug($slug);
        if ($slug === '') {
            return redirect()->to('/notepad');
        }

        $note = $this->notepad->read($slug);
        $data = [
            'page_title' => 'Notepad: ' . $slug,
            'bg' => 'bg-admin',
            'slug' => $slug,
            'note' => $note,
        ];

        $sx = '';
        $sx .= view('Brapci/Headers/header', $data);
        $sx .= view('Brapci/Headers/navbar', $data);
        $sx .= view('Notepad/editor', $data);
        $sx .= view('Brapci/Headers/footer', $data);

        return $sx;
    }

    public function save(string $slug = '')
    {
        $slug = $this->notepad->sanitizeSlug($slug);
        if ($slug === '') {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error',
                'message' => 'Slug invalido.',
            ]);
        }

        $content = (string) $this->request->getPost('content');

        try {
            $saved = $this->notepad->write($slug, $content);
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }

        return $this->response->setJSON([
            'status' => 'ok',
            'slug' => $slug,
            'updated_at' => $saved['updated_at'],
            'csrf' => csrf_hash(),
        ]);
    }
}
