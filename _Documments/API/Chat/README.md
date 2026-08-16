# API de Chat com Ollama

Documentacao da API REST e de streaming do modulo de chat local.

## URL base

Producao:

```text
https://cip.brapci.inf.br/api/ai
```

Desenvolvimento com proxy Angular:

```text
http://localhost:4200/api/ai
```

O prefixo correto e `/api/ai`. A rota `/api/ia` nao existe.

## Autenticacao

Todos os endpoints exigem uma APIKEY ativa vinculada a um usuario. O backend encontra o usuario em `users.us_apikey`, exige `us_apikey_active = 1` e usa o `id_us` encontrado para filtrar projetos, conversas e mensagens.

O cliente nunca deve enviar `user_id`.

### Opcao recomendada: cabecalho HTTP

```http
APIKEY: SUA_APIKEY
Accept: application/json
```

Exemplo:

```bash
curl -H "APIKEY: SUA_APIKEY" \
  -H "Accept: application/json" \
  https://cip.brapci.inf.br/api/ai/chats
```

### Opcao alternativa: query string

```text
https://cip.brapci.inf.br/api/ai/chats?APIKEY=SUA_APIKEY
```

O cabecalho tem prioridade quando as duas formas sao enviadas. A query string deve ser evitada quando possivel, pois URLs podem ser armazenadas no historico do navegador, logs do servidor, proxies e ferramentas de monitoramento.

## Formato das requisicoes

Endpoints `POST` e `PUT` aceitam JSON:

```http
Content-Type: application/json
```

Respostas REST usam JSON. Os endpoints de geracao usam Server-Sent Events (SSE) com `Content-Type: text/event-stream`.

## Resumo dos endpoints

| Metodo | Endpoint | Descricao |
|---|---|---|
| `GET` | `/models` | Lista modelos instalados no Ollama |
| `GET` | `/projects` | Lista projetos do usuario |
| `POST` | `/projects` | Cria um projeto |
| `GET` | `/projects/{id}` | Consulta um projeto |
| `PUT` | `/projects/{id}` | Atualiza um projeto |
| `PATCH` | `/projects/{id}` | Atualiza parcialmente um projeto |
| `DELETE` | `/projects/{id}` | Exclui um projeto |
| `GET` | `/chats` | Lista conversas |
| `POST` | `/chats` | Cria uma conversa |
| `GET` | `/chats/{id}` | Consulta uma conversa |
| `PUT` | `/chats/{id}` | Atualiza uma conversa |
| `DELETE` | `/chats/{id}` | Marca uma conversa como excluida |
| `GET` | `/chats/{id}/messages` | Lista mensagens de uma conversa |
| `POST` | `/chats/{id}/message` | Envia mensagem e recebe resposta em streaming |
| `POST` | `/chats/{id}/regenerate` | Regenera a resposta da ultima pergunta |
| `OPTIONS` | `/api/ai/*` | Preflight CORS atendido pela rota geral da API |

Todos os IDs acessados sao validados contra o usuario identificado pela APIKEY. Um recurso de outro usuario e respondido como nao encontrado.

## Modelos Ollama

### Listar modelos

```http
GET /api/ai/models
```

Exemplo:

```bash
curl -H "APIKEY: SUA_APIKEY" \
  https://cip.brapci.inf.br/api/ai/models
```

Resposta:

```json
{
  "data": [
    {
      "name": "llama3.2:latest",
      "size": 2019393189,
      "modified_at": "2026-08-15T10:00:00Z",
      "details": {}
    }
  ]
}
```

A lista e consultada dinamicamente em `GET /api/tags` do Ollama.

## Projetos

### Listar projetos

```http
GET /api/ai/projects
```

Resposta:

```json
{
  "data": [
    {
      "id": "1",
      "user_id": "10",
      "name": "BRAPCI",
      "description": "Pesquisa em Ciencia da Informacao",
      "system_prompt": "Voce e um assistente especializado.",
      "context": "Contexto persistente do projeto.",
      "default_model": "llama3.2:latest",
      "created_at": "2026-08-15 10:00:00",
      "updated_at": "2026-08-15 10:00:00"
    }
  ]
}
```

### Criar projeto

```http
POST /api/ai/projects
```

Payload:

```json
{
  "name": "BRAPCI",
  "description": "Pesquisa em Ciencia da Informacao",
  "system_prompt": "Voce e um assistente especializado.",
  "context": "Informacoes persistentes usadas nas conversas.",
  "default_model": "llama3.2:latest"
}
```

`name` e obrigatorio e aceita ate 150 caracteres. Os demais campos sao opcionais.

Resposta: `201 Created` com o projeto em `data`.

### Consultar projeto

```http
GET /api/ai/projects/{id}
```

### Atualizar projeto

```http
PUT /api/ai/projects/{id}
```

Atualizacoes parciais tambem podem usar:

```http
PATCH /api/ai/projects/{id}
```

Aceita qualquer subconjunto dos campos:

```json
{
  "name": "Novo nome",
  "description": "Nova descricao",
  "system_prompt": "Novas instrucoes",
  "context": "Novo contexto",
  "default_model": "qwen3:latest"
}
```

### Excluir projeto

```http
DELETE /api/ai/projects/{id}
```

Resposta: `204 No Content`. Conversas vinculadas permanecem armazenadas e passam a ficar sem projeto.

## Conversas

### Listar conversas

```http
GET /api/ai/chats
```

Parametros opcionais:

| Parametro | Padrao | Limite/efeito |
|---|---:|---|
| `project_id` | vazio | Filtra pelo projeto |
| `limit` | `50` | Entre 1 e 100 |
| `offset` | `0` | Deslocamento da paginacao |

Exemplo:

```text
GET /api/ai/chats?project_id=1&limit=20&offset=0
```

Quando a APIKEY tambem for enviada pela URL, acrescente-a com `&APIKEY=...`.

### Criar conversa

```http
POST /api/ai/chats
```

Payload:

```json
{
  "project_id": 1,
  "title": "Nova conversa",
  "model": "llama3.2:latest",
  "system_prompt": "Instrucoes exclusivas desta conversa."
}
```

Campos:

| Campo | Obrigatorio | Descricao |
|---|---|---|
| `project_id` | Nao | Projeto pertencente ao usuario |
| `title` | Nao | Padrao: `Nova conversa`; maximo de 255 caracteres |
| `model` | Condicional | Modelo da conversa; maximo de 150 caracteres |
| `system_prompt` | Nao | Instrucao exclusiva da conversa |

O modelo e escolhido nesta ordem:

1. `model` enviado na conversa;
2. modelo padrao do projeto;
3. modelo padrao do usuario;
4. `OLLAMA_DEFAULT_MODEL`.

Se nenhum modelo estiver disponivel, a API retorna `422`.

Resposta: `201 Created` com a conversa em `data`.

### Consultar conversa

```http
GET /api/ai/chats/{id}
```

### Atualizar conversa

```http
PUT /api/ai/chats/{id}
```

Campos aceitos:

```json
{
  "project_id": 2,
  "title": "Titulo atualizado",
  "model": "qwen3:latest",
  "system_prompt": "Nova instrucao",
  "status": "archived"
}
```

`status` aceita `active` ou `archived` neste endpoint.

### Excluir conversa

```http
DELETE /api/ai/chats/{id}
```

Resposta: `204 No Content`. A exclusao e logica: o status passa para `deleted` e a conversa deixa de aparecer nas consultas normais.

## Mensagens

### Listar mensagens

```http
GET /api/ai/chats/{id}/messages
```

Parametros opcionais:

| Parametro | Padrao | Limite/efeito |
|---|---:|---|
| `limit` | `100` | Entre 1 e 200 |
| `before_id` | vazio | Retorna mensagens anteriores ao ID informado |

Exemplo:

```text
GET /api/ai/chats/1/messages?limit=50&before_id=200
```

Resposta:

```json
{
  "data": [
    {
      "id": "198",
      "chat_id": "1",
      "role": "user",
      "content": "Explique o conceito de ciencia aberta.",
      "model": null,
      "tokens_input": null,
      "tokens_output": null,
      "generation_time_ms": null,
      "status": "completed",
      "request_id": "msg-20260815-001",
      "created_at": "2026-08-15 10:10:00"
    }
  ]
}
```

## Envio com streaming

### Enviar mensagem

```http
POST /api/ai/chats/{id}/message
Content-Type: application/json
Accept: text/event-stream
```

Payload:

```json
{
  "content": "Explique o conceito de ciencia aberta.",
  "request_id": "msg-20260815-001"
}
```

`content` e obrigatorio e aceita ate 50.000 caracteres. `request_id` e opcional, aceita ate 64 caracteres e deve ser unico na conversa. Seu uso e recomendado para impedir mensagens duplicadas em retries.

A mensagem do usuario e persistida antes da chamada ao Ollama.

### Eventos SSE

Cada evento possui as linhas `event` e `data`, separadas por uma linha vazia.

Token recebido:

```text
event: token
data: {"type":"token","content":"Ciencia"}

```

Finalizacao:

```text
event: done
data: {"type":"done","message_id":199,"model":"llama3.2:latest"}

```

Erro durante o stream:

```text
event: error
data: {"type":"error","message":"Ollama indisponivel ou falha durante a geracao."}

```

A resposta completa do assistente e persistida ao final com modelo, tempo de geracao e tokens quando fornecidos pelo Ollama.

### Exemplo com cURL

Use `-N` para desabilitar o buffer do cURL:

```bash
curl -N -X POST \
  -H "APIKEY: SUA_APIKEY" \
  -H "Content-Type: application/json" \
  -H "Accept: text/event-stream" \
  --data '{"content":"Explique ciencia aberta.","request_id":"msg-001"}' \
  https://cip.brapci.inf.br/api/ai/chats/1/message
```

Com APIKEY na URL:

```bash
curl -N -X POST \
  -H "Content-Type: application/json" \
  -H "Accept: text/event-stream" \
  --data '{"content":"Explique ciencia aberta.","request_id":"msg-001"}' \
  "https://cip.brapci.inf.br/api/ai/chats/1/message?APIKEY=SUA_APIKEY"
```

### Exemplo com Fetch

```typescript
const response = await fetch(
  'https://cip.brapci.inf.br/api/ai/chats/1/message',
  {
    method: 'POST',
    credentials: 'include',
    headers: {
      APIKEY: apiKey,
      Accept: 'text/event-stream',
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({
      content: 'Explique ciencia aberta.',
      request_id: crypto.randomUUID(),
    }),
    signal: abortController.signal,
  },
);

if (!response.ok || !response.body) {
  throw new Error(`Falha HTTP: ${response.status}`);
}

const reader = response.body.getReader();
const decoder = new TextDecoder();

while (true) {
  const { done, value } = await reader.read();
  if (done) break;

  const chunk = decoder.decode(value, { stream: true });
  // Acumule o texto e separe eventos por "\n\n".
  console.log(chunk);
}
```

Para interromper a requisicao:

```typescript
abortController.abort();
```

## Regenerar resposta

```http
POST /api/ai/chats/{id}/regenerate
Accept: text/event-stream
```

O endpoint localiza a ultima mensagem `user`, remonta o contexto anterior a ela e gera uma nova resposta. O formato dos eventos e o mesmo de `/message`.

Exemplo:

```bash
curl -N -X POST \
  -H "APIKEY: SUA_APIKEY" \
  -H "Accept: text/event-stream" \
  https://cip.brapci.inf.br/api/ai/chats/1/regenerate
```

## Contexto enviado ao modelo

O backend monta as mensagens nesta ordem:

1. `OLLAMA_SYSTEM_PROMPT`;
2. system prompt do projeto;
3. contexto persistente do projeto;
4. system prompt da conversa;
5. historico recente;
6. mensagem atual.

O numero padrao de mensagens historicas e controlado por `OLLAMA_HISTORY_MESSAGES`.

## Codigos HTTP e erros

| Codigo | Significado |
|---:|---|
| `200` | Consulta ou atualizacao concluida; stream iniciado |
| `201` | Projeto ou conversa criado |
| `204` | Recurso excluido sem corpo de resposta |
| `401` | APIKEY ausente, invalida ou inativa |
| `404` | Projeto ou conversa inexistente ou pertencente a outro usuario |
| `409` | `request_id` ja processado na conversa |
| `422` | Payload invalido, modelo ausente ou impossibilidade de regenerar |
| `500` | Erro interno ou configuracao obrigatoria ausente |
| `503` | Ollama indisponivel ou falha de comunicacao |

APIKEY ausente:

```json
{
  "error": "authentication_required",
  "message": "APIKEY nao informada."
}
```

APIKEY invalida ou inativa:

```json
{
  "error": "invalid_apikey",
  "message": "APIKEY invalida ou inativa."
}
```

Erro de validacao:

```json
{
  "errors": {
    "name": "The name field is required."
  }
}
```

Depois que uma resposta SSE comeca, falhas sao informadas por evento `error`; o status HTTP permanece `200` porque os headers ja foram enviados.

## CORS

Origens atualmente permitidas:

```text
http://localhost:4200
http://127.0.0.1:4200
https://brapci.inf.br
https://cip.brapci.inf.br
```

O servidor permite credenciais e o cabecalho `APIKEY`. Chamadas Angular com cookies devem usar `withCredentials: true`; chamadas Fetch usam `credentials: 'include'`.

## Configuracao do servidor

Variaveis disponiveis:

```ini
OLLAMA_URL=http://localhost:11434
OLLAMA_DEFAULT_MODEL=
OLLAMA_TIMEOUT=120
OLLAMA_HISTORY_MESSAGES=40
OLLAMA_SYSTEM_PROMPT=
```

As tabelas `ai_projects`, `ai_chats`, `ai_messages` e `ai_user_settings` ficam no grupo de banco `brapci_ai`, atualmente mapeado para o database `brapci_ia`.

Para executar as migrations:

```bash
php spark migrate -g brapci_ai
```

## Fluxo recomendado do cliente

1. Obter a APIKEY no login.
2. Consultar `/models` e `/projects` em paralelo.
3. Criar ou selecionar um projeto.
4. Criar uma conversa informando o modelo.
5. Carregar `/chats/{id}/messages`.
6. Enviar mensagens por `/chats/{id}/message` com `request_id` unico.
7. Atualizar somente a mensagem em geracao a cada evento `token`.
8. Consolidar a mensagem ao receber `done`.
9. Exibir falhas recebidas pelo evento `error`.
10. Cancelar a requisicao com `AbortController` quando solicitado pelo usuario.


curl.exe -N -X POST `
  -H "Content-Type: application/json" `
  -H "Accept: text/event-stream" `
  --data-raw '{"content":"Olá, responda apenas: conexão funcionando.","request_id":"teste-chat-5-003"}' `
  "https://cip.brapci.inf.br/api/ai/chats/5/message?APIKEY=ff63a314d1ddd425517550f446e4175e"


  curl "https://cip.brapci.inf.br/api/ai/chats/5?APIKEY=ff63a314d1ddd425517550f446e4175e"

  curl "https://cip.brapci.inf.br/api/ai/chats/5?APIKEY=SUA_APIKEY"
