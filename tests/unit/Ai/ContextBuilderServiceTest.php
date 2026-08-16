<?php

namespace Tests\Unit\Ai;

use App\Services\Ai\ContextBuilderService;
use CodeIgniter\Test\CIUnitTestCase;

final class ContextBuilderServiceTest extends CIUnitTestCase
{
    public function testBuildKeepsPromptsContextHistoryAndCurrentMessageInOrder(): void
    {
        $service = new ContextBuilderService();
        $messages = $service->build(
            ['system_prompt' => 'Instrucao da conversa'],
            ['system_prompt' => 'Instrucao do projeto', 'context' => 'Contexto BRAPCI'],
            [
                ['role' => 'system', 'content' => 'nao duplicar'],
                ['role' => 'user', 'content' => 'pergunta anterior'],
                ['role' => 'assistant', 'content' => 'resposta anterior'],
            ],
            'pergunta atual',
        );

        $this->assertSame([
            ['role' => 'system', 'content' => 'Instrucao do projeto'],
            ['role' => 'system', 'content' => "Contexto persistente do projeto:\nContexto BRAPCI"],
            ['role' => 'system', 'content' => 'Instrucao da conversa'],
            ['role' => 'user', 'content' => 'pergunta anterior'],
            ['role' => 'assistant', 'content' => 'resposta anterior'],
            ['role' => 'user', 'content' => 'pergunta atual'],
        ], $messages);
    }

    public function testBuildLimitsHistoryWithoutRemovingCurrentMessage(): void
    {
        putenv('OLLAMA_HISTORY_MESSAGES=2');
        $_ENV['OLLAMA_HISTORY_MESSAGES'] = '2';
        $service = new ContextBuilderService();
        $messages = $service->build([], null, [
            ['role' => 'user', 'content' => 'um'],
            ['role' => 'assistant', 'content' => 'dois'],
            ['role' => 'user', 'content' => 'tres'],
        ], 'quatro');

        $this->assertSame(['dois', 'tres', 'quatro'], array_column($messages, 'content'));
        putenv('OLLAMA_HISTORY_MESSAGES');
        unset($_ENV['OLLAMA_HISTORY_MESSAGES']);
    }
}
