<?php
$details = $wiki['dados'] ?? [];
$photos = $wiki['fotos'] ?? [];
$id = (int) $pensador['id'];
?>
<main class="content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><?= esc($pensador['nome']) ?></h1>
        <a href="<?= site_url('labs/thinkers') ?>" class="btn btn-outline-secondary">Voltar</a>
    </div>
    <?php foreach (['success' => 'success', 'error' => 'danger', 'warning' => 'warning'] as $key => $style): ?>
        <?php if ($message = session()->getFlashdata($key)): ?>
            <div class="alert alert-<?= $style ?>" role="alert"><?= esc($message) ?></div>
        <?php endif; ?>
    <?php endforeach; ?>
    <div class="d-flex flex-wrap gap-2 mb-4">
        <?php foreach (['wikidata' => 'Wikidata', 'wikipedia' => 'Wikipédia'] as $source => $label): ?>
            <form method="post" action="<?= site_url('labs/thinkers/' . $id . '/wiki/' . $source) ?>" class="thinker-import">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-primary" <?= empty($pensador['link_' . $source]) ? 'disabled' : '' ?>><i class="bi bi-arrow-clockwise" aria-hidden="true"></i> Atualizar do <?= esc($label) ?></button>
            </form>
        <?php endforeach; ?>
        <a href="<?= site_url('labs/thinkers/' . $id . '/edit') ?>" class="btn btn-outline-primary">Editar cadastro</a>
    </div>
    <?php if (empty($pensador['link_wikidata']) || empty($pensador['link_wikipedia'])): ?>
        <p class="text-muted">Preencha os links no cadastro para habilitar a atualização de cada fonte.</p>
    <?php endif; ?>
    <div class="row g-4">
        <div class="col-lg-7">
            <section class="card card-dashboard p-4">
                <dl class="mb-0">
                    <dt>Nome completo</dt>
                    <dd><?= esc($details['nome_completo'] ?? $pensador['nome']) ?></dd>
                    <dt>Nome de citação</dt>
                    <dd><?= esc($pensador['nome_citacao']) ?></dd>
                    <?php foreach (['data_nascimento' => 'Data de nascimento', 'data_falecimento' => 'Data de falecimento', 'local_nascimento' => 'Local de nascimento', 'pais_nascimento' => 'País de nascimento'] as $field => $label): ?>
                        <dt><?= esc($label) ?></dt>
                        <dd><?= esc($details[$field] ?? 'Não informado') ?></dd>
                    <?php endforeach; ?>
                    <dt>Instituições afiliadas</dt>
                    <dd>
                        <?php if (!empty($details['instituicoes'])): ?>
                            <ul>
                                <?php foreach ($details['instituicoes'] as $institution): ?>
                                    <li><?= esc($institution['nome']) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>Não informado<?php endif; ?>
                    </dd>
                    <?php foreach (['link_wikipedia' => 'Wikipédia', 'link_wikidata' => 'Wikidata'] as $field => $label): ?>
                        <dt><?= esc($label) ?></dt>
                        <dd>
                            <?php if (filter_var($pensador[$field], FILTER_VALIDATE_URL) && in_array(strtolower((string) parse_url($pensador[$field], PHP_URL_SCHEME)), ['http', 'https'], true)): ?>
                                <a href="<?= esc($pensador[$field], 'attr') ?>" target="_blank" rel="noopener noreferrer"><?= esc($pensador[$field]) ?></a>
                            <?php else: ?>Não informado<?php endif; ?>
                        </dd>
                    <?php endforeach; ?>
                </dl>
                <?php if (!empty($wiki['fontes']['wikipedia']['resumo'])): ?>
                    <h2 class="h5 mt-3">Sobre o pensador</h2>
                    <p><?= nl2br(esc($wiki['fontes']['wikipedia']['resumo'])) ?></p>
                <?php endif; ?>
                <?php foreach ($wiki['fontes'] ?? [] as $source => $snapshot): ?>
                    <p class="small text-muted mb-1">Atualização <?= esc($source) ?>: <?= esc($snapshot['atualizado_em']) ?><?= !empty($snapshot['wikidata_id']) ? ' · Wikidata ' . esc($snapshot['wikidata_id']) : '' ?></p>
                <?php endforeach; ?>
            </section>
        </div>
        <div class="col-lg-5">
            <section class="card card-dashboard p-3">
                <h2 class="h5">Fotos do pensador</h2>
                <?php if (!$photos): ?>
                    <p class="text-muted mb-0">Nenhuma foto importada.</p>
                <?php else: ?>
                    <div id="thinkerPhotos" class="carousel slide carousel-dark" data-bs-interval="false" role="region" aria-label="Fotos do pensador">
                        <div class="carousel-inner">
                            <?php foreach ($photos as $index => $photo): ?>
                                <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                                    <img src="<?= base_url('_repository/thinkers/' . $id . '/' . rawurlencode($photo['arquivo'])) ?>" class="d-block w-100" style="height: 400px; object-fit: contain;" alt="<?= esc($pensador['nome'], 'attr') ?> — foto <?= $index + 1 ?>">
                                    <div class="text-center small mt-3 px-4">
                                        <p class="mb-1">Foto <?= $index + 1 ?> de <?= count($photos) ?></p>
                                        <p class="mb-1"><?= esc($photo['autor'] ?? '') ?></p>
                                        <p class="mb-1"><?= esc($photo['licenca'] ?? '') ?></p>
                                        <?php if (preg_match('~^https://(?:commons\.wikimedia\.org|[a-z-]+\.wikipedia\.org)/~', $photo['descricao_url'] ?? '')): ?>
                                            <a href="<?= esc($photo['descricao_url'], 'attr') ?>" target="_blank" rel="noopener noreferrer">Fonte da imagem</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php if (count($photos) > 1): ?>
                            <button class="carousel-control-prev" type="button" data-bs-target="#thinkerPhotos" data-bs-slide="prev"><span class="carousel-control-prev-icon" aria-hidden="true"></span><span class="visually-hidden">Foto anterior</span></button>
                            <button class="carousel-control-next" type="button" data-bs-target="#thinkerPhotos" data-bs-slide="next"><span class="carousel-control-next-icon" aria-hidden="true"></span><span class="visually-hidden">Próxima foto</span></button>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </section>
        </div>
    </div>
</main>
<script>
document.querySelectorAll('.thinker-import').forEach(form => {
    form.addEventListener('submit', () => {
        document.querySelectorAll('.thinker-import button').forEach(button => { button.disabled = true; });
        form.querySelector('button').textContent = 'Atualizando…';
    });
});
</script>
