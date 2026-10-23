# Coleta Z39.50 solicitada pelo FIND

Aplicar a migration CreateZ3950Queue no grupo default do Brapci3.1 (tabela z3950_queue).
O comando `php spark migrate` aplica todas as migrations pendentes; revisar as demais antes de executar em produção.

POST /api/find/z3950/queue com JSON {"isbn":"9786589167501"} retorna HTTP 202 com id, isbn e status. ISBN-10 é validado e convertido para ISBN-13. Pedidos repetidos compartilham o mesmo job, inclusive após conclusão.
GET /api/find/z3950/queue/{isbn} retorna o estado e o resultado MARCXML. Estados: pending, processing, completed, not_found e failed.

Agendar a cada minuto no Brapci3.1: `php spark z3950:work`.
É necessária a extensão PHP-YAZ no PHP CLI. Cada execução processa até 20 pedidos; cinco tentativas com espera exponencial, recuperação de tarefas interrompidas após cinco minutos e trava por pedido para concorrência.
Configuração opcional: metadata.z3950Server = z3950.bn.br:210/biblioteca.

No FIND, cada insert bem-sucedido pelo modelo Find/Items/Index com ISBN válido grava um pedido em writable/z3950-outbox e tenta enviá-lo à API, com timeout de três segundos. Identificadores artificiais de obras sem ISBN são ignorados.
Agendar a cada minuto no FIND, dentro de v2: `php spark z3950:retry`.
Configurar metadata.queueUrl no .env do FIND para o endereço do Brapci instalado. Padrão: https://cip.brapci.inf.br/api/find/z3950/queue.
Pedidos só são removidos da caixa local após HTTP 202 com confirmação do ISBN e ID. Erros são registrados no log e não desfazem o cadastro do item.

A coleta fica armazenada na fila do Brapci. A importação automática do MARCXML para os campos do FIND é uma etapa distinta.
Os comandos precisam ser agendados no servidor de implantação; adicionar os arquivos não cria tarefas do sistema operacional.
