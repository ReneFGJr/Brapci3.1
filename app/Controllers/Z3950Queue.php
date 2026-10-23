<?php
namespace App\Controllers;

class Z3950Queue extends BaseController
{
    public function process()
    {
        $payload = $this->request->getJSON(true);
        $value = is_array($payload) ? ($payload['isbn'] ?? '') : '';
        $isbn = is_string($value) ? \App\Libraries\Z3950Isbn::normalize($value) : '';
        if ($isbn === '') return $this->response->setStatusCode(422)->setJSON(['error' => 'ISBN inválido.']);
        if (!function_exists('yaz_connect')) {
            return $this->response->setStatusCode(503)->setJSON(['error' => 'Extensão PHP-YAZ indisponível no servidor.']);
        }
        $queue = new \App\Models\Z3950\Queue();
        if (!$queue->where('isbn', $isbn)->first()) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Pedido não encontrado.']);
        }
        $collector = new \App\Models\Z3950\Index();
        $queue->processOne([$collector, 'searchByISBN'], $isbn);
        $job = $queue->where('isbn', $isbn)->first();
        unset($job['lock_token']);
        $job['trace'] = $collector->trace;
        $job['trace'][] = ['phase' => 'queue', 'detail' => 'Estado na fila: ' . $job['status']];
        return $this->response->setJSON($job);
    }

    public function create()
    {
        $request = $this->request;
        $payload = stripos($request->getHeaderLine('Content-Type'), 'application/json') !== false
            ? $request->getJSON(true) : $request->getPost();
        $isbn = is_array($payload) ? ($payload['isbn'] ?? '') : '';
        if (!is_string($isbn)) $isbn = '';
        try {
            $job = (new \App\Models\Z3950\Queue())->enqueue($isbn);
            return $this->response->setStatusCode(202)->setJSON([
                'id' => (int) $job['id'], 'isbn' => $job['isbn'], 'status' => $job['status'],
            ]);
        } catch (\InvalidArgumentException $e) {
            return $this->response->setStatusCode(422)->setJSON(['error' => $e->getMessage()]);
        }
    }

    public function show(string $isbn)
    {
        $isbn = \App\Libraries\Z3950Isbn::normalize($isbn);
        $job = $isbn === '' ? null : (new \App\Models\Z3950\Queue())->where('isbn', $isbn)->first();
        if (!$job) return $this->response->setStatusCode(404)->setJSON(['error' => 'Pedido não encontrado.']);
        unset($job['lock_token']);
        return $this->response->setJSON($job);
    }
}
