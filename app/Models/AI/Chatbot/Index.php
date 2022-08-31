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
		'id_m', 'm_message','m_ip'
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

	function chat()
		{
			$sx = $this->chart_html();
			echo $sx;
			exit;
			return $sx;
		}

	function query()
		{
			$dd = array();
			//echo '===>'.$_POST['messageValue'];
			$key = get("msg");

			/************* Ativa LOG */
			if (strlen($key != ''))
				{
					$dd['m_message'] = $key;
					$dd['m_ip'] = ip();
					$this->insert($dd);
				}

			echo $key.'?';
			exit;
		}

	function chart_html()
	{
		$data['title'] = 'Chatbot';
		$sx = view('Brapci/Headers/header',$data);
		$sx = '
			<link rel="stylesheet" href="' . URL . '/css/chat_bot.css">
			<div class="col-12">
				<div id="header">
					Brapci ChatBot
					<br><sup style="font-size: 50%;">Idade mental: 0 anos.</sup>
				</div>

				<div id="body">
				<!-- This section will be dynamically inserted from JavaScript -->
					<div class="userSection">
					<div class="messages user-message">

					</div>
					<div class="seperator"></div>
					</div>
					<div class="botSection">
					<div class="messages bot-reply">

					</div>
					<div class="seperator"></div>
					</div>
				</div>

				<div id="inputArea">
				<input type="text" name="messages" id="userInput" placeholder="'.lang('brapci.chat_answer'). '" required >
				<input type="submit" id="send" value="Send">
				</div>
			</div>


			<script type="text/javascript">

				document.querySelector("#send").addEventListener("click", async () => {
					let xhr = new XMLHttpRequest();
					var userMessage = document.querySelector("#userInput").value

					let userHtml = \'<div class="userSection">\' + \'<div class="messages user-message">\'+userMessage+\'</div>\'+
					\'<div class="seperator"></div>\'+\'</div>\'

					document.querySelector(\'#body\').innerHTML+= userHtml;

					xhr.open("POST", "'.PATH.COLLECTION.'/chat/query/?msg="+userMessage);
					xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
					xhr.send();

					xhr.onload = function () {
						let botHtml = \'<div class="botSection">\'+\'<div class="messages bot-reply">\'+this.responseText+\'</div>\'+
						\'<div class="seperator"></div>\'+\'</div>\'

						document.querySelector(\'#body\').innerHTML+= botHtml;
					}
				})
			</script>';

			return $sx;
	}
}
