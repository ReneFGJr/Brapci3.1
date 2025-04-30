<?php

namespace App\Controllers;

use App\Controllers\BaseController;

helper(['form']);
helper(['boostrap', 'url', 'sisdoc_forms', 'form', 'nbr', 'sessions', 'cookie']);
$session = \Config\Services::session();

define("URL", getenv("app.baseURL"));
define("PATH", getenv("app.baseURL") . getenv("app.baseURL.prefix"));
define("COLLECTION", '/catalog');
define("PREFIX", '');
define("MODULE", 'catalog');
define("LIBRARY", '0000');
define("URLa",'https://cip.brapci.inf.br/cdu/');


class Cdu extends BaseController
{
    public function index($act = '', $d1 = '', $d2 = '', $d3 = '', $d4 = '')
    {
        $sx = '';
        $sx .= view('CDU/header');
        switch ($act) {
            case '':
                $sx = 'CDU - Classificação Decimal Universal';
                break;
            case 'test':
                $sx = $this->test($d1);
                break;
            case 'avaliation':
                $sx = $this->avaliation();
                break;
            case 'import':
                $sx = $this->import();
                break;
            case 'questions':
                $sx = $this->questions();
                break;
            default:
                $sx = 'HELLO';
        }
        return $sx;
    }

    function test($cracha)
        {
            $sx = '';
            $sx .= view('CDU/header');
            $Students = new \App\Models\CDU\Students();
            $Avaliation = new \App\Models\CDU\Avaliation();
            $dtu = $Students->getStudent($cracha);
            if ($dtu == []) {
                return 'NOK';
            } else {
                $sx .= view('CDU/aluno/profile', $dtu);
                $Avaliation->makeTest($cracha);
                $sx .= $Avaliation->showTest($cracha);
            }
            return $sx;
        }

    public function questions()
    {
        $data = [];
        $data = [
            'validation' => null,
        ];

        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'q_statement' => 'required|string',
                'q_ask'       => 'required|string',
                'q_comentary' => 'permit_empty|string',
                'q_group'     => 'required|string',
            ];
            $errors = [
                'q_statement' => ['required' => 'Enunciado é obrigatório.'],
                'q_ask'       => ['required' => 'Pergunta é obrigatória.'],
                'q_comentary' => ['string'   => 'Comentário inválido.'],
                'q_group'     => ['required' => 'Grupo é obrigatório.'],
            ];

            if (! $this->validate($rules, $errors)) {
                $data['validation'] = $this->validator;
                echo "ERRO";
            } else {
                $model = new \App\Models\CDU\Questions();

                $model->insert([
                    'id_q'        => $this->request->getPost('id_q'),
                    'q_statement' => $this->request->getPost('q_statement'),
                    'q_ask'       => $this->request->getPost('q_ask'),
                    'q_comentary' => $this->request->getPost('q_comentary'),
                    'q_group'     => $this->request->getPost('q_group'),
                ]);
                return redirect()->to(URLa . ('/cdu/questions'));
            }
        }
        $data['url'] = URLa . ('/cdu/questions');
        return view('CDU/sistema/question_input', $data);
    }


    function import()
    {
        $data = [];

        $meth = UpperCase($this->request->getMethod());
        if ($meth === 'POST') {
            $rules = [
                'mensagem' => [
                    'label'  => 'Mensagem',
                    'rules'  => 'required',
                    'errors' => [
                        'required' => 'O campo {field} não pode ficar vazio.',
                    ],
                ],
            ];

            if (! $this->validate($rules)) {
                $data['validation'] = $this->validator;
            } else {
                $texto = $this->request->getPost('mensagem');
                $Students = new \App\Models\CDU\Students();
                $data['message'] = $Students->import($texto);
                // … trate o $texto conforme necessário …
                //return redirect()->to('/formulario/sucesso');
            }
        }
        $data['title'] = 'Importação de Alunos';
        $data['description'] = 'Cole aqui a lista de alunos, um por linha. Exemplo:';
        $data['sample'] = '1	GRAD	123123	FULANA PEIXOTO SAILVA	';
        $data['url'] = URLa.('/cdu/import');
        return view('/CDU/sistema/textarea', $data);
    }

    public function avaliation()
    {
        // Carrega o helper de formulários e o serviço de validação
        helper(['form']);
        $data = [];

        // Se vier um POST, valida
        if ($this->request->getMethod() === 'post') {
            // Define regras: required, numeric, exatamente 8 dígitos
            $rules = [
                'cracha' => [
                    'label'  => 'Crachá',
                    'rules'  => 'required|numeric|exact_length[8]',
                    'errors' => [
                        'required'     => 'O campo {field} é obrigatório.',
                        'numeric'      => 'O campo {field} deve conter apenas números.',
                        'exact_length' => 'O campo {field} deve ter exatamente {param} dígitos.',
                    ],
                ],
            ];

            if (! $this->validate($rules)) {
                // Se falhar, envia os erros para a view
                $data['validation'] = $this->validator;
            } else {
                // Se passar, trate o dado (por exemplo, salvar ou redirecionar)
                $cracha = $this->request->getPost('cracha');
                // … faça o que for preciso com $cracha …
                echo "O";
                exit;
                return $this->test($cracha);
            }
        }
        $data['url'] = URLa . ('avaliation');
        return view('CDU/aluno/formulario', $data);
    }
}
