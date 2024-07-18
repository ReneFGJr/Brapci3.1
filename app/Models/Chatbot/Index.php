<?php

namespace App\Models\Chatbot;

use CodeIgniter\Model;

class Index extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'indices';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    function index($d1, $d2, $d3, $d4)
    {
        return $this->js().
                $this->style().
                $this->screen();
    }

    function js()
    {
        $sx = "
            <script>
            // script.js
            document.getElementById('send-btn').addEventListener('click', () => {
                const userInput = document.getElementById('user-input').value;
                if (userInput) {
                    addMessage(userInput, 'user');
                    document.getElementById('user-input').value = '';
                    fetchResponse(userInput);
                }
            });

            function addMessage(message, sender) {
                const messageElement = document.createElement('div');
                messageElement.classList.add('message', sender);
                messageElement.textContent = message;
                document.getElementById('messages').appendChild(messageElement);
                document.getElementById('messages').scrollTop = document.getElementById('messages').scrollHeight;
            }

            async function fetchResponse(userInput) {
                try {
                    const response = await fetch('http://localhost:PORT/chat', { // Substitua PORT pelo número da porta do seu servidor local
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Authorization': 'Bearer YOUR_API_KEY' // Se necessário
                        },
                        body: JSON.stringify({ message: userInput })
                    });

                    const data = await response.json();
                    if (data && data.message) {
                        addMessage(data.message, 'bot');
                    } else {
                        addMessage('Desculpe, algo deu errado.', 'bot');
                    }
                } catch (error) {
                    addMessage('Desculpe, algo deu errado.', 'bot');
                }
            }
            </script>
            ";
        return $sx;
    }
    function screen()
    {
        $sx = '
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Chatbot</title>
        </head>
        <body>
            <div id="chatbot-container">
                <div id="chatbox">
                    <div id="messages"></div>
                    <input type="text" id="user-input" placeholder="Digite sua mensagem aqui...">
                    <button id="send-btn">Enviar</button>
                </div>
            </div>
            <script src="script.js"></script>
        </body>
        </html>
        ';
        return $sx;
    }

    function style()
    {
        $sx = '
            <style>
            /* styles.css */
            body {
                font-family: Arial, sans-serif;
                background-color: #f0f0f0;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                margin: 0;
            }

            #chatbot-container {
                width: 400px;
                height: 600px;
                background-color: white;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                border-radius: 10px;
                overflow: hidden;
                display: flex;
                flex-direction: column;
            }

            #chatbox {
                flex: 1;
                display: flex;
                flex-direction: column;
            }

            #messages {
                flex: 1;
                padding: 10px;
                overflow-y: auto;
            }

            #user-input {
                border: none;
                padding: 10px;
                flex: none;
                width: calc(100% - 20px);
                margin: 10px;
                border-radius: 5px;
                box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
            }

            #send-btn {
                border: none;
                background-color: #007bff;
                color: white;
                padding: 10px;
                flex: none;
                margin: 10px;
                border-radius: 5px;
                cursor: pointer;
            }

            #send-btn:hover {
                background-color: #0056b3;
            }
            </style>
            ';
        return $sx;
    }
}
