<div class="container mt-5">
    <div class="row">
        <div class="col-12">
            <h1>OAI-PMH Server</h1>
            <p class="lead">Servidor de Protocolo de Harvesting de Metadados Aberto</p>

            <div class="alert alert-info" role="alert">
                <h4 class="alert-heading">Bem-vindo ao OAI-Server!</h4>
                <p>Este é um servidor OAI-PMH em conformidade com a especificação Open Archives Initiative.</p>
                <hr>
                <p class="mb-0">Para usar este servidor, acesse a URL com os parâmetros apropriados do OAI-PMH.</p>
            </div>

            <?php if (!empty($path)): ?>
            <div class="card mt-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Verbos Disponíveis para: <code><?= htmlspecialchars($path) ?></code></h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <h6><strong>Identify</strong></h6>
                            <p class="text-muted">Informações sobre o repositório</p>
                            <a href="<?= getenv('app.baseURL') . '/oai-server/' . htmlspecialchars($path) . '?verb=Identify' ?>"
                               class="btn btn-sm btn-outline-primary" target="_blank">
                                Executar
                            </a>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6><strong>ListSets</strong></h6>
                            <p class="text-muted">Lista os conjuntos disponíveis</p>
                            <a href="<?= getenv('app.baseURL') . '/oai-server/' . htmlspecialchars($path) . '?verb=ListSets' ?>"
                               class="btn btn-sm btn-outline-primary" target="_blank">
                                Executar
                            </a>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6><strong>ListMetadataFormats</strong></h6>
                            <p class="text-muted">Formatos de metadados suportados</p>
                            <a href="<?= getenv('app.baseURL') . '/oai-server/' . htmlspecialchars($path) . '?verb=ListMetadataFormats' ?>"
                               class="btn btn-sm btn-outline-primary" target="_blank">
                                Executar
                            </a>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6><strong>ListIdentifiers</strong></h6>
                            <p class="text-muted">Lista identifiers dos registros</p>
                            <a href="<?= getenv('app.baseURL') . '/oai-server/' . htmlspecialchars($path) . '?verb=ListIdentifiers&metadataPrefix=oai_dc' ?>"
                               class="btn btn-sm btn-outline-primary" target="_blank">
                                Executar
                            </a>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6><strong>ListRecords</strong></h6>
                            <p class="text-muted">Lista todos os registros completos</p>
                            <a href="<?= getenv('app.baseURL') . '/oai-server/' . htmlspecialchars($path) . '?verb=ListRecords&metadataPrefix=oai_dc' ?>"
                               class="btn btn-sm btn-outline-primary" target="_blank">
                                Executar
                            </a>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6><strong>GetRecord</strong></h6>
                            <p class="text-muted">Obtém um registro específico</p>
                            <a href="<?= getenv('app.baseURL') . '/oai-server/' . htmlspecialchars($path) . '?verb=GetRecord&metadataPrefix=oai_dc&identifier=oai:' . htmlspecialchars($path) . ':article:1' ?>"
                               class="btn btn-sm btn-outline-primary" target="_blank">
                                Executar (ID: 1)
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <div class="card mt-4">
                <div class="card-header">
                    <h5>Verbos Disponíveis</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <li class="list-group-item"><strong>Identify</strong> - Fornece informações sobre o repositório</li>
                        <li class="list-group-item"><strong>ListSets</strong> - Lista os conjuntos disponíveis</li>
                        <li class="list-group-item"><strong>ListMetadataFormats</strong> - Lista os formatos de metadados</li>
                        <li class="list-group-item"><strong>ListIdentifiers</strong> - Lista identifiers dos registros</li>
                        <li class="list-group-item"><strong>ListRecords</strong> - Lista todos os registros</li>
                        <li class="list-group-item"><strong>GetRecord</strong> - Obtém um registro específico</li>
                    </ul>
                </div>
            </div>
            <?php endif; ?>

            <div class="card mt-4">
                <div class="card-header">
                    <h5>Exemplo de Requisição</h5>
                </div>
                <div class="card-body">
                    <p>GET <code><?= getenv('app.baseURL') . '/oai-server/repository-name?verb=Identify' ?></code></p>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <h5>Documentação</h5>
                </div>
                <div class="card-body">
                    <p>Para mais informações sobre o protocolo OAI-PMH, visite:</p>
                    <a href="https://www.openarchives.org/OAI/openarchivesprotocol.html" target="_blank" class="btn btn-outline-primary">
                        OAI-PMH Specification
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
