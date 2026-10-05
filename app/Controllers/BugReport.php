<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\Functions\Bugs;
use App\Models\Socials;

class BugReport extends Controller
{
    private function reportSchemaReady(): bool
    {
        $db = db_connect();
        return $db->fieldExists('bug_url', 'bugs') && $db->fieldExists('bug_description', 'bugs');
    }

    private function schemaUnavailable()
    {
        log_message('error', 'Bug reports require migration 2026-10-05-120000_AddBugReportDetails.');
        return $this->response->setStatusCode(503)->setJSON([
            'message' => 'O serviço de relatos precisa de uma atualização no banco de dados. A equipe deve aplicar a migração AddBugReportDetails.',
        ]);
    }

    public function user($apikey = '')
    {
        helper(['boostrap', 'url', 'sisdoc_forms', 'form', 'nbr', 'sessions', 'cookie']);
        $this->response->setHeader('Cache-Control', 'no-store');
        if (strtoupper($this->request->getMethod()) === 'OPTIONS') {
            return $this->response->setStatusCode(204);
        }
        $user = $apikey !== '' ? (new Socials())->validToken($apikey) : [];
        if ((string)($user['status'] ?? '') !== '200' || empty($user['ID'])) {
            return $this->response->setStatusCode(401)->setJSON(['message' => 'APIKEY inválida.']);
        }
        if (!$this->reportSchemaReady()) {
            return $this->schemaUnavailable();
        }
        $reports = (new Bugs())->select('id_bug, bug_v, bug_problem, bug_status, bug_solution, bug_url, bug_description')
            ->where('bug_user', (int)$user['ID'])->orderBy('id_bug', 'DESC')->findAll();
        return $this->response->setJSON(['bugs' => $reports]);
    }

    public function form()
    {
        helper(['boostrap', 'url', 'sisdoc_forms', 'form', 'nbr', 'sessions', 'cookie']);
        $this->response->setHeader('Cache-Control', 'no-store');
        $method = strtoupper($this->request->getMethod());
        if ($method === 'OPTIONS') {
            return $this->response->setStatusCode(204);
        }
        $input = $this->request->getPost();
        if (strpos($this->request->getHeaderLine('Content-Type'), 'application/json') !== false) {
            $input = $this->request->getJSON(true) ?? [];
        }
        if (!is_array($input)) {
            return $this->response->setStatusCode(422)->setJSON(['message' => 'Parâmetros inválidos.']);
        }
        $token = $input['token'] ?? preg_replace('/^Bearer\s+/i', '', $this->request->getHeaderLine('Authorization'));
        $user = is_string($token) && $token !== '' ? (new Socials())->validToken($token) : [];
        if ((string)($user['status'] ?? '') !== '200' || empty($user['ID'])) {
            return $this->response->setStatusCode(401)->setJSON(['message' => 'Entre na sua conta para reportar problemas.', 'login' => '/signin']);
        }
        $problems = [
            ['value' => 'pdfIncorrect', 'label' => 'PDF incorreto'],
            ['value' => 'pdfInaccessible', 'label' => 'PDF inacessível'],
            ['value' => 'other', 'label' => 'Outro'],
            ['value' => 'authorincorrect', 'label' => 'Autor incorreto'],
        ];
        $action = $input['action'] ?? 'form';
        if ($method === 'GET' || $action === 'form') {
            return $this->response->setJSON([
                'problems' => $problems,
                'fields' => [
                    ['name' => 'id', 'type' => 'integer', 'required' => true, 'min' => 1],
                    ['name' => 'problem', 'type' => 'select', 'required' => true],
                    ['name' => 'url', 'type' => 'url', 'maxLength' => 4096],
                    ['name' => 'description', 'type' => 'textarea', 'requiredWhen' => ['problem' => 'other'], 'maxLength' => 2000],
                ],
            ]);
        }
        $id = filter_var($input['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $problem = $input['problem'] ?? '';
        $description = is_string($input['description'] ?? '') ? trim($input['description'] ?? '') : '';
        if ($action !== 'submit' || !$id || !is_string($problem) || !in_array($problem, array_column($problems, 'value'), true)
            || ($problem === 'other' && $description === '') || mb_strlen($description) > 2000) {
            return $this->response->setStatusCode(422)->setJSON(['message' => 'Informe um ID válido, o problema e a descrição para Outro (até 2000 caracteres).']);
        }
        $url = $input['url'] ?? '';
        if (!is_string($url) || mb_strlen($url) > 4096 || ($url !== '' &&
            (!filter_var($url, FILTER_VALIDATE_URL) || !in_array(strtolower(parse_url($url, PHP_URL_SCHEME) ?? ''), ['http', 'https'], true)))) {
            return $this->response->setStatusCode(422)->setJSON(['message' => 'URL inválida.']);
        }
        if (!$this->reportSchemaReady()) {
            return $this->schemaUnavailable();
        }
        // Use the existing brapci bugs model and its pending/resolved status convention.
        $bugs = new Bugs();
        if ($problem !== 'other' && $bugs->where('bug_v', $id)->where('bug_problem', $problem)->where('bug_user', (int)$user['ID'])->where('bug_status', 1)->first()) {
            return $this->response->setStatusCode(202)->setJSON(['message' => 'Esse problema já foi registrado.']);
        }
        try {
            $bugId = $bugs->insert([
                'bug_name' => $user['user'], 'bug_user' => $user['ID'],
                'bug_problem' => $problem, 'bug_IP' => $this->request->getIPAddress(),
                'bug_status' => 1, 'bug_v' => $id,
                // Keep the original description separate from the team's response.
                'bug_solution' => '', 'bug_description' => $description, 'bug_url' => $url,
            ]);
            if (!$bugId) {
                throw new \RuntimeException('Bug insert failed');
            }
        } catch (\Throwable $exception) {
            log_message('error', 'Bug report persistence failed: {message}', ['message' => $exception->getMessage()]);
            return $this->response->setStatusCode(500)->setJSON(['message' => 'Não foi possível registrar o problema.']);
        }
        return $this->response->setStatusCode(201)->setJSON(['message' => 'Problema registrado com sucesso.', 'id' => $bugId]);
    }
}
