<?php
namespace App\Controllers;

class Z3950Queue extends BaseController
{
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
