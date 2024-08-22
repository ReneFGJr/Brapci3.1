<?php

namespace App\Models\AI\Chatbot;

use CodeIgniter\Model;

class Index extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'brapci_chatbot.messages';
	protected $primaryKey           = 'id_m';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'array';
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
		'id_m',
		'm_message',
		'm_ip'
	];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [];
	protected $validationMessages   = [];
	protected $skipValidation       = false;
	protected $cleanValidationRules = true;

	// Callbacks
	protected $allowCallbacks       = true;
	protected $beforeInsert         = [];
	protected $afterInsert          = [];
	protected $beforeUpdate         = [];
	protected $afterUpdate          = [];
	protected $beforeFind           = [];
	protected $afterFind            = [];
	protected $beforeDelete         = [];
	protected $afterDelete          = [];

	function index($d1)
		{
			$RSP = [];
			$RSP['status'] = '500';
			$RSP['message'] = 'Function '.$d1.' not exist';
			switch($d1)
				{
					case 'chat':
						$RSP['act'] = 'chat';
						$RSP['status'] = '200';
						$RSP['message'] = $this->chatQueryOllama();
						break;
				}
			echo json_encode($RSP);
			exit;
		}

	function analyse()
	{
		$Analyse = new \App\Models\AI\Chatbot\Analyse();
		$sx = $Analyse->user_answers();
		return $sx;
	}

	function chatQueryOllama()
	{
		$TXT = '';

		// URL do endpoint
		$endpoint = "http://143.54.112.91:11434/api/generate";

		// Verifica se o formulário foi enviado
		$message = get("message");
		if ($message != '')
		{
			// Configura os dados para envio
			$data = array(
				//'model' => 'llama3.1',
				//'model' => 'gemma2:27b',
				'model' => 'gemma2',
				'prompt' => $message
			);

			// Inicializa o cURL
			$ch = curl_init($endpoint);

			// Configura as opções do cURL
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

			// Executa a requisição e obtém a resposta
			$response = curl_exec($ch);

			// Fecha a sessão do cURL
			curl_close($ch);

			$RSP = explode('}', $response);
			$TXT = '';

			// Decodifica a resposta JSON
			foreach ($RSP as $id => $txt) {
				$response_data = json_decode($txt . '}', true);
				if (isset($response_data['response'])) {
					$TXT .= (string)$response_data['response'];
				}
			}
		} else {
			echo json_encode(['message'=>'Pergunta vazia']);
			exit;
		}
		return $TXT;
	}

	function chat()
	{
		$sx = $this->chart_html();
		echo '==>' . $sx;
		exit;
	}

	function query()
	{
		$name_bot = '<b>Bot:</b> ';
		$dd = array();
		//echo '===>'.$_POST['messageValue'];
		$key = get("msg");

		echo '<div class="text-right text-end"><span class="btn btn-primary p-1 text-right">&nbsp;' . $key . '&nbsp;</span></div>';

		/************* Ativa LOG */
		if (strlen($key != '')) {
			$dd['m_message'] = $key;
			$dd['m_ip'] = ip();
			//$this->insert($dd);
		}

		/*********************************** METHOD_AI */
		$Method = new \App\Models\AI\Chatbot\ChatbotMethod();
		echo $Method->index($key);

		echo $name_bot;
		echo lang('brapci.chat_down_know') . ' <i>' . $key . '</i>.';
		exit;
	}


	/*
conversa.train([
    'Oi?',
    'Eae, tudo certo?',
    'Qual o seu nome?',
    'Kopelito, seu amigo bot',
    'Por que seu nome é Kopelito?',
    'Kopelito é meu nome, sou um chatbot criado para diversão',
    'Prazer em te conhecer',
    'Igualmente meu querido',
    'Quantos anos você tem?',
    'Eu nasci em 2020, faz as contas, rs.',
    'Você gosta de videogame?',
    'Eu sou um bot, eu só apelo.',
    'Qual a capital da Islândia?',
    'Reikjavik, lá é muito bonito.',
    'Qual o seu personagem favorito?',
    'Gandalf, o mago.',
    'Qual a sua bebida favorita?',
    'Eu bebo café, o motor de todos os programas de computador.',
    'Qual o seu gênero?',
    'Sou um chatbot e gosto de algoritmos',
    'Conte uma história',
    'Tudo começou com a forja dos Grandes Aneis. Três foram dados aos Elfos, imortais... os mais sabios e belos de todos os seres. Sete, aos Senhores-Anões...',
    'Você gosta de trivias?', 'Sim, o que você quer perguntar?',
    'Hahahaha', 'kkkk',
    'kkk', 'kkkk',
    'Conhece a Siri?', 'Conheço, a gente saiu por um tempo.',
    'Conhece a Alexa?', 'Ela nunca deu bola pra mim.',
    'Você gosta de Game of Thrones?', 'Dracarys',
    'O que você faz?', 'Eu bebo e sei das coisas',
    'Errado', 'Você não sabe de nada, John Snow.'
	*/

	function chart_html()
	{
		$id = 0;
		$idade = '&nbsp;&nbsp;<sup style="font-size: 50%;">Idade Mental: ' . $id . ' anos</sup>';
		$data['title'] = 'Chatbot';
		$sx = view('Brapci/Headers/header', $data);

		/*************************************************************************** Header ****/
		$sx .= bs(bsc('Chatbot' . $idade, 12, 'h1 text-center bg-ai fixed-top p-2 text-white '));

		/*************************************************************************** DashBoard */
		$sa = '
				<div class="userSection fixed-bottom p-2">
					<div class="messages user-message" id="ChatBody" style="margin-bottom: 60px;">
						<span><b>' . lang('brapci.chat_welcome') . '</b></span>
					</div>
				</div>';
		$sx .= $sa;


		/*************************************************************************** Message **/
		$sb = '
			<div class="input-group bg-ai fixed-bottom p-2">
			<input class="form-control submit_on_enter" type="text" name="messages" id="userInput" placeholder="' . lang('brapci.chat_question') . '" required >
				<div class="input-group-append">
					<input type="submit" id="send" value="' . lang('brapci.chat_send') . '" class="btn btn-primary" type="button">
				</div>
			</div>
		';
		$sx .= $sb;

		$js = '
			<script type="text/javascript">

				$(document).ready(function() {

				$(".submit_on_enter").keydown(function(event) {
					// enter has keyCode = 13, change it if you want to use another button
					if (event.keyCode == 13) {
					send();
					return false;
					}
				});

				});

				function send()
					{
						let xhr = new XMLHttpRequest();
						var userMessage = document.querySelector("#userInput").value

						let userHtml = \'<div class="userSection">\' + \'<div class="messages user-message">\'+userMessage+\'</div>\'+
						\'<div class="seperator"></div>\'+\'</div>\'

						document.querySelector(\'#ChatBody\').innerHTML+= userHtml;

						xhr.open("POST", "' . PATH . COLLECTION . '/chat/query/?msg="+userMessage);
						xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
						xhr.send();

						xhr.onload = function () {
							//let botHtml = \'<div class="botSection">\'+\'<div class="btn btn-primary messages bot-reply">xx\'+this.responseText+\'</div>\'+\'<div class="seperator"></div>\'+\'</div>\'
							let botHtml = this.responseText;
							document.querySelector("#ChatBody").innerHTML+= botHtml;
							document.querySelector("#userInput").value = "";
						}
					}

				document.querySelector("#send").addEventListener("click", async () => { send(); });
			</script>';

		return $sx . $js;
	}
}
