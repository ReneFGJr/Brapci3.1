# SKILL — Chat Local com Angular, CodeIgniter 4, MySQL e Ollama

## Rota

IMPLEMENTE A CAMADA DO BACKEND NO CI4 e PHP em /app

## 1. Objetivo

Desenvolver um sistema de chat web integrado ao Ollama executado localmente.

O sistema deve:

- utilizar Angular no frontend;
- utilizar uma API backend como intermediária entre Angular, MySQL e Ollama;
- permitir selecionar dinamicamente um dos modelos instalados no Ollama;
- armazenar conversas e mensagens no MySQL;
- vincular projetos, conversas e mensagens ao usuário autenticado;
- impedir acesso ao chat por usuários não autenticados;
- permitir a criação de projetos com contexto próprio;
- utilizar o contexto do projeto automaticamente nas conversas;
- oferecer respostas em streaming;
- possuir interface moderna, agradável, responsiva e simples;
- ser modular e preparado para futura integração com RAG, documentos, embeddings e ElasticSearch.

## 2. Princípios obrigatórios

Priorizar, nesta ordem:

1. segurança;
2. separação de responsabilidades;
3. simplicidade;
4. legibilidade;
5. modularidade;
6. reutilização;
7. experiência do usuário;
8. performance.

Não duplicar lógica.

Não criar componentes Angular monolíticos.

Não misturar regras de interface, banco de dados e comunicação com Ollama.

Antes de alterar código existente, inspecionar a estrutura do projeto e reutilizar padrões, serviços, autenticação, componentes e convenções já existentes sempre que forem adequados.

Não substituir componentes funcionais sem necessidade.

## 3. Arquitetura

Utilizar obrigatoriamente:

```text
Angular
   |
   v
Backend API
   |       \
   v        v
 MySQL    Ollama
```

O Angular NÃO deve acessar diretamente:

- MySQL;
- Ollama;
- filesystem do servidor.

Toda comunicação com Ollama deve passar pelo backend.

Toda persistência deve passar pelo backend.

O backend é responsável por:

- autenticação;
- autorização;
- ownership dos dados;
- projetos;
- conversas;
- mensagens;
- montagem de contexto;
- integração com Ollama;
- streaming;
- persistência;
- validação.

## 4. Backend

Preferir CodeIgniter 4 quando o projeto existente já utilizar CI4.

Organizar a implementação em camadas equivalentes a:

```text
Controllers
Models
Services
Libraries
Filters
Routes
Database/Migrations
```

Criar serviços específicos, evitando concentrar toda a lógica no Controller.

Estrutura conceitual:

```text
ChatController
ProjectController
ModelController

ChatService
ProjectService
OllamaService
ContextBuilderService
```

Controllers devem ser pequenos.

Controllers devem:

1. receber a requisição;
2. validar entrada;
3. identificar o usuário autenticado;
4. chamar o serviço apropriado;
5. devolver a resposta.

Regras de negócio devem ficar nos Services.

## 5. Autenticação

O chat somente pode ser acessado por usuários autenticados.

No Angular, proteger:

```text
/chat
/chat/:id
/projects
/projects/:id
```

Utilizar AuthGuard ou mecanismo equivalente já existente.

Usuário não autenticado deve ser redirecionado para:

```text
/login
```

O backend também deve exigir autenticação.

O AuthGuard NÃO substitui a autorização no servidor.

## 6. Regra de segurança de usuário

Nunca confiar em `user_id` enviado pelo frontend.

O backend deve obter o usuário exclusivamente da sessão, JWT ou mecanismo de autenticação existente.

Nunca implementar autorização desta forma:

```text
user_id recebido pelo Angular
```

Utilizar conceitualmente:

```text
user_id = usuário autenticado no backend
```

Toda consulta de projeto ou conversa deve validar ownership.

Exemplo:

```sql
SELECT *
FROM ai_chats
WHERE id = ?
AND user_id = ?;
```

Aplicar a mesma regra para:

- visualizar;
- listar;
- editar;
- excluir;
- arquivar;
- enviar mensagens;
- regenerar respostas;
- alterar modelo;
- acessar projetos.

Um usuário nunca pode acessar dados pertencentes a outro usuário.

## 7. Ollama

O servidor padrão é:

```text
http://localhost:11434
```

O endereço deve ser configurável por variável de ambiente.

Exemplo:

```text
OLLAMA_URL=http://localhost:11434
```

Não hardcode URLs do Ollama nos componentes Angular.

Criar `OllamaService` no backend.

Esse serviço deve centralizar:

- consulta aos modelos;
- chat;
- streaming;
- tratamento de erros;
- timeout;
- cancelamento quando aplicável;
- parâmetros de geração.

## 8. Modelos Ollama

Não manter uma lista fixa de modelos no Angular.

O backend deve consultar dinamicamente os modelos disponíveis no Ollama.

Criar endpoint equivalente a:

```text
GET /api/ai/models
```

O Angular deve apresentar os modelos retornados em um seletor.

Quando um novo modelo for instalado no Ollama, ele deve poder aparecer no sistema sem alteração no código.

O modelo selecionado deve ser armazenado na conversa.

Cada projeto pode possuir um modelo padrão.

Ordem sugerida para escolha:

```text
modelo escolhido na conversa
        ↓
modelo padrão do projeto
        ↓
modelo padrão do usuário
        ↓
modelo padrão da aplicação
```

Sempre mostrar claramente ao usuário qual modelo está ativo.

## 9. Projetos

O usuário pode criar projetos para organizar conversas relacionadas.

Cada projeto deve possuir pelo menos:

- id;
- user_id;
- name;
- description;
- system_prompt;
- context;
- default_model;
- created_at;
- updated_at.

Um projeto pode possuir várias conversas.

Uma conversa pode existir sem projeto.

Representação:

```text
Usuário
   |
   +-- Projeto A
   |      |
   |      +-- Conversa 1
   |      +-- Conversa 2
   |
   +-- Projeto B
   |      |
   |      +-- Conversa 3
   |
   +-- Conversas sem projeto
```

## 10. Contexto do projeto

O contexto do projeto deve ser automaticamente incorporado às chamadas ao LLM.

Não obrigar o usuário a repetir o contexto em cada conversa.

Exemplo:

```text
Projeto: BRAPCI

Descrição:
Pesquisa e desenvolvimento relacionado à BRAPCI.

System prompt:
Você é um assistente especializado em Ciência da Informação.

Contexto:
Informações persistentes relevantes para as conversas deste projeto.
```

Separar semanticamente:

- descrição;
- instruções/system prompt;
- contexto.

Não misturar esses campos sem necessidade.

## 11. Context Builder

Criar um serviço dedicado à construção do contexto.

Não montar prompts complexos diretamente:

- no Angular;
- no Controller;
- no OllamaService.

Utilizar `ContextBuilderService` ou equivalente.

Ordem conceitual:

```text
SYSTEM PROMPT GLOBAL
        +
SYSTEM PROMPT DO PROJETO
        +
CONTEXTO DO PROJETO
        +
SYSTEM PROMPT DA CONVERSA
        +
HISTÓRICO NECESSÁRIO
        +
MENSAGEM ATUAL
```

O Context Builder deve permitir evolução futura sem reescrever o chat.

Preparar para futura incorporação de:

- RAG;
- PDFs;
- documentos;
- embeddings;
- busca vetorial;
- ElasticSearch;
- bases de conhecimento;
- ferramentas externas.

## 12. Gerenciamento da janela de contexto

Não assumir que todo o histórico poderá ser enviado indefinidamente ao modelo.

Preparar estratégia para:

1. manter mensagens recentes;
2. respeitar a janela de contexto do modelo;
3. futuramente resumir mensagens antigas;
4. preservar instruções importantes do projeto;
5. incorporar apenas contexto relevante quando houver RAG.

A persistência completa no MySQL é diferente do contexto enviado ao LLM.

Nunca excluir mensagens do banco apenas porque não serão enviadas ao modelo.

## 13. Banco de dados

Utilizar MySQL.

Não armazenar toda a conversa em um único JSON.

Separar pelo menos:

```text
ai_projects
ai_chats
ai_messages
ai_user_settings
```

## 14. Tabela de projetos

Estrutura de referência:

```sql
CREATE TABLE ai_projects (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,

    name VARCHAR(150) NOT NULL,
    description TEXT NULL,

    system_prompt LONGTEXT NULL,
    context LONGTEXT NULL,

    default_model VARCHAR(150) NULL,

    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_project_user (user_id)
);
```

Adaptar foreign keys à tabela de usuários existente.

## 15. Tabela de conversas

Estrutura de referência:

```sql
CREATE TABLE ai_chats (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    user_id BIGINT UNSIGNED NOT NULL,
    project_id BIGINT UNSIGNED NULL,

    title VARCHAR(255) NOT NULL,

    model VARCHAR(150) NOT NULL,

    system_prompt LONGTEXT NULL,

    status ENUM('active', 'archived', 'deleted')
        DEFAULT 'active',

    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_chat_user (user_id),
    INDEX idx_chat_project (project_id),
    INDEX idx_chat_updated (updated_at),

    CONSTRAINT fk_chat_project
        FOREIGN KEY (project_id)
        REFERENCES ai_projects(id)
        ON DELETE SET NULL
);
```

## 16. Tabela de mensagens

Estrutura de referência:

```sql
CREATE TABLE ai_messages (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    chat_id BIGINT UNSIGNED NOT NULL,

    role ENUM(
        'system',
        'user',
        'assistant',
        'tool'
    ) NOT NULL,

    content LONGTEXT NOT NULL,

    model VARCHAR(150) NULL,

    tokens_input INT UNSIGNED NULL,
    tokens_output INT UNSIGNED NULL,

    generation_time_ms INT UNSIGNED NULL,

    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_message_chat (chat_id),
    INDEX idx_message_created (created_at),

    CONSTRAINT fk_message_chat
        FOREIGN KEY (chat_id)
        REFERENCES ai_chats(id)
        ON DELETE CASCADE
);
```

## 17. Preferências do usuário

Estrutura opcional:

```sql
CREATE TABLE ai_user_settings (
    user_id BIGINT UNSIGNED PRIMARY KEY,

    default_model VARCHAR(150) NULL,

    temperature DECIMAL(3,2) DEFAULT 0.70,

    num_ctx INT UNSIGNED DEFAULT 8192,

    stream TINYINT(1) DEFAULT 1,

    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
);
```

## 18. API

Criar endpoints REST equivalentes a:

```text
GET    /api/ai/models

GET    /api/ai/projects
POST   /api/ai/projects
GET    /api/ai/projects/{id}
PUT    /api/ai/projects/{id}
DELETE /api/ai/projects/{id}

GET    /api/ai/chats
POST   /api/ai/chats
GET    /api/ai/chats/{id}
PUT    /api/ai/chats/{id}
DELETE /api/ai/chats/{id}

GET    /api/ai/chats/{id}/messages

POST   /api/ai/chats/{id}/message
POST   /api/ai/chats/{id}/regenerate
```

Adaptar os nomes às convenções existentes no projeto.

Todos os endpoints de dados pessoais devem exigir autenticação.

## 19. Envio de mensagem

Fluxo obrigatório:

```text
Usuário envia mensagem
        ↓
Angular chama backend
        ↓
Backend identifica usuário autenticado
        ↓
Backend valida ownership da conversa
        ↓
Mensagem do usuário é validada
        ↓
Mensagem é persistida
        ↓
ContextBuilder monta contexto
        ↓
OllamaService chama Ollama
        ↓
Resposta chega por streaming
        ↓
Angular atualiza interface progressivamente
        ↓
Resposta final é persistida
        ↓
Métricas são registradas quando disponíveis
```

Evitar duplicação de mensagens em caso de retry.

## 20. Streaming

Streaming é requisito prioritário.

Não aguardar a resposta inteira do Ollama para atualizar a tela.

Durante geração:

- mostrar estado visual;
- atualizar a resposta progressivamente;
- impedir envios duplicados acidentais;
- permitir interromper a geração;
- preservar a pergunta do usuário em caso de falha.

Ao finalizar:

- persistir a resposta completa;
- atualizar `updated_at` da conversa;
- registrar modelo;
- registrar tempo de geração;
- registrar tokens quando disponíveis.

A tecnologia de streaming entre backend e Angular pode utilizar SSE, streaming HTTP ou mecanismo equivalente compatível com a arquitetura existente.

## 21. Angular

Criar uma feature de chat organizada.

Estrutura sugerida:

```text
src/app/chat/

components/
    chat-layout/
    chat-sidebar/
    chat-header/
    chat-message/
    chat-input/
    model-selector/
    project-selector/
    project-dialog/
    typing-indicator/

pages/
    chat-page/
    project-page/

services/
    chat.service.ts
    project.service.ts
    streaming.service.ts

guards/
    chat-auth.guard.ts

models/
    chat.model.ts
    message.model.ts
    project.model.ts
    ollama-model.model.ts

chat.routes.ts
```

Adaptar à estrutura existente quando necessário.

## 22. TypeScript

Utilizar tipagem estrita.

Evitar:

```typescript
any
```

Criar interfaces ou types para:

```text
User
Project
Chat
Message
OllamaModel
ChatRequest
ChatResponse
StreamingEvent
```

Não duplicar interfaces equivalentes em componentes diferentes.

## 23. Estado da interface

Controlar explicitamente estados como:

```text
idle
loading
streaming
completed
error
cancelled
```

A interface deve deixar claro quando o modelo está processando.

Evitar múltiplas requisições simultâneas na mesma conversa, salvo se isso for deliberadamente implementado.

## 24. Layout

Criar interface inspirada em sistemas modernos de chat, mas sem copiar visualmente uma aplicação específica.

Estrutura:

```text
+---------------------------------------------------------+
| Menu | Projeto/Conversa             Modelo ativo        |
+----------------+----------------------------------------+
|                |                                        |
| + Novo chat    |             Mensagens                  |
|                |                                        |
| Projetos       |                                        |
|   Projeto A    |                                        |
|     Chat 1     |                                        |
|     Chat 2     |                                        |
|                |                                        |
| Recentes       |                                        |
|   Chat 3       |                                        |
|                |                                        |
+----------------+----------------------------------------+
|                | Digite uma mensagem...          Enviar |
+----------------+----------------------------------------+
```

## 25. Diretrizes visuais

A interface deve ser:

- limpa;
- moderna;
- discreta;
- responsiva;
- confortável para leitura prolongada.

Usar:

- boa hierarquia tipográfica;
- espaçamento consistente;
- sidebar recolhível;
- largura confortável para leitura;
- contraste adequado;
- estados de hover e focus;
- ícones consistentes;
- modo claro/escuro se compatível com a aplicação existente.

Evitar:

- excesso de bordas;
- excesso de cores;
- gradientes desnecessários;
- animações excessivas;
- componentes visualmente pesados.

## 26. Sidebar

A sidebar deve apresentar, quando aplicável:

```text
+ Novo chat

Projetos
    Projeto A
    Projeto B

Conversas recentes

Busca

Configurações
```

Permitir recolher a sidebar.

Em telas pequenas, utilizar drawer ou comportamento equivalente.

## 27. Cabeçalho do chat

Mostrar claramente:

- projeto ativo;
- título da conversa;
- modelo ativo.

Permitir selecionar o modelo.

Se a troca de modelo durante uma conversa for permitida, registrar qual modelo produziu cada resposta.

## 28. Mensagens

Diferenciar visualmente:

```text
user
assistant
system
tool
```

Normalmente, mensagens `system` e `tool` não precisam ser exibidas ao usuário final, salvo em modo de depuração.

Mensagens do assistant devem suportar:

- Markdown;
- títulos;
- listas;
- tabelas;
- links;
- citações textuais;
- blocos de código;
- código inline.

Sanitizar todo HTML resultante.

## 29. Blocos de código

Oferecer:

- syntax highlighting;
- identificação da linguagem;
- botão copiar;
- scroll horizontal quando necessário.

Nunca executar automaticamente código produzido pelo modelo.

## 30. Campo de mensagem

O editor deve ser expansível.

Comportamento:

```text
Enter       = enviar
Shift+Enter = nova linha
```

Adicionar botão de envio.

Durante streaming, substituir ou complementar o botão de envio com ação para interromper geração.

O campo deve continuar utilizável e visualmente estável em mensagens longas.

## 31. Recursos da conversa

Planejar suporte para:

- novo chat;
- renomear conversa;
- excluir conversa;
- arquivar conversa;
- copiar resposta;
- editar pergunta;
- regenerar resposta;
- interromper geração;
- trocar modelo;
- mover conversa para projeto.

A implementação inicial pode ser incremental.

## 32. Títulos automáticos

Uma nova conversa pode começar com título provisório:

```text
Nova conversa
```

Após a primeira interação, o sistema pode futuramente gerar um título curto automaticamente.

Não tornar a geração automática de título requisito para o funcionamento básico.

## 33. Tratamento de erros

Tratar explicitamente:

- Ollama indisponível;
- nenhum modelo instalado;
- modelo removido;
- timeout;
- erro de banco;
- conversa inexistente;
- acesso não autorizado;
- sessão expirada;
- falha no streaming.

Nunca mostrar stack trace ao usuário final.

Registrar detalhes técnicos no backend.

Exibir mensagem amigável na interface.

## 34. Segurança de conteúdo

Sanitizar Markdown e HTML.

Proteger contra XSS.

Não utilizar diretamente HTML produzido pelo modelo com bypass de segurança.

Não executar scripts retornados pelo LLM.

Não permitir que conteúdo gerado pelo modelo altere a aplicação.

## 35. Segurança do Ollama

Não expor a porta do Ollama diretamente para clientes externos sem necessidade.

Preferir:

```text
Browser
   ↓
Backend autenticado
   ↓
Ollama
```

O backend controla quem pode usar os modelos.

## 36. Variáveis de ambiente

Não armazenar credenciais ou configuração sensível no código.

Utilizar `.env`.

Exemplos:

```text
OLLAMA_URL=http://localhost:11434
OLLAMA_DEFAULT_MODEL=
OLLAMA_TIMEOUT=
```

Não versionar arquivos `.env` contendo segredos.

Manter `.env.example` quando apropriado.

## 37. Performance

Evitar recarregar todo o histórico após cada token.

Durante streaming, atualizar somente a mensagem em geração.

Paginar conversas quando a quantidade crescer.

Preparar paginação de mensagens para conversas muito longas.

Criar índices adequados no banco.

Evitar consultas N+1.

## 38. Persistência

Persistir a mensagem do usuário antes ou no início seguro da chamada ao modelo.

Persistir a resposta do assistant ao finalizar.

Definir estratégia para respostas interrompidas.

Se uma resposta parcial for armazenada, registrar status correspondente.

Evitar criar duas mensagens do usuário quando a requisição for reenviada.

## 39. Evolução para RAG

Não implementar RAG no núcleo inicial se não for solicitado.

Entretanto, não criar uma arquitetura que impeça sua inclusão.

Arquitetura futura:

```text
Projeto
   ↓
Documentos
   ↓
Extração
   ↓
Chunks
   ↓
Embeddings
   ↓
Índice vetorial / ElasticSearch
   ↓
Recuperação
   ↓
ContextBuilder
   ↓
Ollama
```

O contexto recuperado deve ser incorporado pelo `ContextBuilderService`.

## 40. Projetos com documentos

Preparar conceitualmente o projeto para futuramente aceitar:

- PDF;
- DOCX;
- TXT;
- Markdown;
- páginas HTML;
- datasets;
- referências;
- outros documentos.

Não implementar upload ou processamento documental sem necessidade no escopo atual.

## 41. Logs

Registrar no backend, quando apropriado:

- erros do Ollama;
- duração da requisição;
- modelo utilizado;
- falhas de streaming;
- erros de autorização;
- falhas de persistência.

Não registrar:

- senhas;
- tokens de autenticação;
- segredos;
- dados sensíveis desnecessários.

## 42. Testes obrigatórios

Testar pelo menos:

### Autenticação

- usuário não autenticado não acessa chat;
- sessão expirada é tratada;
- rota Angular é protegida;
- API também é protegida.

### Autorização

- usuário A não visualiza projeto de B;
- usuário A não abre conversa de B;
- usuário A não exclui conversa de B;
- usuário A não envia mensagem em conversa de B.

### Ollama

- modelos são carregados dinamicamente;
- modelo selecionado é utilizado;
- Ollama offline produz erro tratado;
- modelo inexistente produz erro tratado.

### Conversas

- nova conversa é criada;
- mensagem é persistida;
- resposta é persistida;
- histórico pode ser retomado;
- conversa pertence ao usuário correto.

### Projetos

- projeto é criado;
- contexto é armazenado;
- conversa pode ser vinculada ao projeto;
- contexto do projeto chega ao Context Builder;
- usuário só vê seus projetos.

### Streaming

- resposta aparece progressivamente;
- geração pode ser interrompida;
- erro intermediário não quebra a interface;
- mensagem do usuário não é perdida.

## 43. Acessibilidade

Aplicar boas práticas de acessibilidade.

Garantir:

- navegação por teclado;
- focus visível;
- labels adequados;
- contraste suficiente;
- botões com nomes acessíveis;
- estados não comunicados apenas por cor;
- áreas de mensagem semanticamente compreensíveis.

## 44. Responsividade

Desktop:

```text
sidebar + chat
```

Mobile:

```text
drawer + chat
```

O campo de entrada deve permanecer acessível.

Blocos de código e tabelas não devem quebrar o layout.

## 45. Processo de implementação

Executar em etapas.

### Etapa 1 — Inspeção

Antes de criar arquivos:

- identificar versão do Angular;
- identificar estrutura do frontend;
- identificar backend;
- identificar autenticação existente;
- identificar tabela de usuários;
- identificar padrão de rotas;
- identificar padrão visual;
- identificar bibliotecas já instaladas.

Não presumir detalhes que possam ser verificados no projeto.

### Etapa 2 — Banco

Criar migrations para:

```text
ai_projects
ai_chats
ai_messages
ai_user_settings
```

Adaptar foreign keys ao banco existente.

### Etapa 3 — Segurança

Implementar:

- autenticação;
- autorização;
- ownership;
- proteção das rotas.

Testar antes de avançar.

### Etapa 4 — Ollama

Implementar `OllamaService`.

Primeiro garantir:

```text
listar modelos
```

Depois:

```text
enviar chat
```

Depois:

```text
streaming
```

### Etapa 5 — Projetos

Implementar CRUD de projetos.

Sempre filtrar pelo usuário autenticado.

### Etapa 6 — Conversas

Implementar:

- criar;
- listar;
- abrir;
- renomear;
- arquivar/excluir.

### Etapa 7 — Mensagens

Implementar:

- envio;
- persistência;
- recuperação do histórico.

### Etapa 8 — Context Builder

Centralizar a montagem de mensagens enviadas ao Ollama.

### Etapa 9 — Streaming

Integrar backend, Ollama e Angular.

### Etapa 10 — Interface

Criar componentes Angular reutilizáveis.

### Etapa 11 — Markdown

Adicionar renderização segura e syntax highlighting.

### Etapa 12 — UX

Implementar:

- loading;
- cancelar;
- copiar;
- regenerar;
- tratamento de erros;
- responsividade.

### Etapa 13 — Testes

Executar testes de:

- autenticação;
- autorização;
- isolamento de usuário;
- projetos;
- chats;
- modelos;
- streaming;
- erros.

## 46. Regras para o agente de código

Ao executar esta SKILL:

1. primeiro inspecionar o projeto;
2. não assumir framework/backend se ele puder ser identificado;
3. apresentar um plano curto antes de mudanças grandes;
4. alterar apenas arquivos necessários;
5. preservar padrões existentes;
6. não reescrever autenticação funcional sem necessidade;
7. não instalar dependências sem verificar se já existe solução equivalente;
8. não adicionar bibliotecas pesadas para tarefas simples;
9. criar código tipado e documentado quando necessário;
10. executar lint/build/testes disponíveis após alterações relevantes;
11. corrigir erros introduzidos antes de considerar a etapa concluída;
12. informar arquivos criados e alterados;
13. informar migrations que precisam ser executadas;
14. informar novas variáveis de ambiente;
15. não declarar sucesso sem verificar build ou testes quando estes estiverem disponíveis.

## 47. Critérios de conclusão

O núcleo do sistema estará concluído quando:

1. usuário não autenticado não conseguir acessar `/chat`;
2. API rejeitar chamadas não autenticadas;
3. usuário visualizar somente seus projetos;
4. usuário visualizar somente suas conversas;
5. modelos instalados no Ollama aparecerem dinamicamente;
6. usuário puder selecionar um modelo;
7. usuário puder iniciar uma conversa;
8. mensagens forem persistidas no MySQL;
9. histórico puder ser retomado;
10. respostas forem apresentadas em streaming;
11. usuário puder interromper uma geração;
12. usuário puder criar projetos;
13. projeto puder possuir contexto próprio;
14. contexto do projeto for enviado automaticamente ao modelo;
15. conversas puderem ser vinculadas a projetos;
16. Markdown for renderizado de forma segura;
17. código puder ser copiado;
18. interface funcionar em desktop e mobile;
19. falhas do Ollama forem tratadas adequadamente;
20. testes de isolamento entre usuários forem aprovados.

## 48. Fora do escopo inicial

Não implementar automaticamente, salvo solicitação específica:

- agentes autônomos;
- execução de comandos do sistema;
- execução automática de código gerado;
- acesso irrestrito ao filesystem;
- navegação web pelo modelo;
- RAG completo;
- embeddings;
- banco vetorial;
- upload e indexação de documentos;
- compartilhamento público de conversas;
- colaboração entre usuários.

Esses recursos devem ser tratados como evoluções posteriores.

## 49. Resultado esperado

O resultado deve ser um chat local seguro e agradável, no qual:

```text
Usuário autenticado
        ↓
seleciona/cria projeto
        ↓
abre/cria conversa
        ↓
seleciona modelo Ollama
        ↓
envia mensagem
        ↓
backend monta contexto
        ↓
Ollama gera resposta
        ↓
resposta aparece em streaming
        ↓
conversa fica persistida no MySQL
```

A arquitetura deve permitir que o sistema evolua posteriormente para um ambiente de projetos com documentos, RAG e recuperação de informação sem exigir reescrita completa do módulo de chat.
