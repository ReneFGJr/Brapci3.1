<?php

namespace App\Models\AI\Chatbot;

use CodeIgniter\Model;

class ChatbotMethod_Introduction extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = '*';
    protected $primaryKey           = 'id';
    protected $useAutoIncrement     = true;
    protected $insertID             = 0;
    protected $returnType           = 'array';
    protected $useSoftDeletes       = false;
    protected $protectFields        = true;
    protected $allowedFields        = [];

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

    function index($q='')
        {

        }
}
