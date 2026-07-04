<?php
$slug = $slug ?? '';
$content = (string) ($note['content'] ?? '');
$saveUrl = base_url('notepad/' . $slug);
$backUrl = base_url('notepad');
?>

<div class="container-fluid py-3">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <h3 class="mb-0">notepad/<?= esc($slug) ?></h3>
            <small class="text-muted">Edicao livre. Salve manualmente ou aguarde o autosave.</small>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="<?= esc($backUrl) ?>" class="btn btn-outline-secondary btn-sm">Voltar</a>
            <button id="saveButton" class="btn btn-primary btn-sm">Salvar agora</button>
            <span id="saveStatus" class="small text-muted">Pronto</span>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <textarea id="noteContent" class="form-control border-0" style="min-height: 72vh; font-family: monospace; font-size: 16px; line-height: 1.45;" spellcheck="false"><?= esc($content) ?></textarea>
        </div>
    </div>
</div>

<script>
(function () {
    const textarea = document.getElementById('noteContent');
    const saveButton = document.getElementById('saveButton');
    const saveStatus = document.getElementById('saveStatus');
    const saveUrl = <?= json_encode($saveUrl) ?>;
    const csrfName = <?= json_encode(csrf_token()) ?>;
    let csrfHash = <?= json_encode(csrf_hash()) ?>;

    let dirty = false;
    let saving = false;

    const setStatus = (text, css) => {
        saveStatus.textContent = text;
        saveStatus.className = 'small ' + css;
    };

    const save = async () => {
        if (saving) return;
        saving = true;
        setStatus('Salvando...', 'text-warning');

        const formData = new FormData();
        formData.append('content', textarea.value);
        formData.append(csrfName, csrfHash);

        try {
            const res = await fetch(saveUrl, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await res.json();
            if (!res.ok || data.status !== 'ok') {
                throw new Error(data.message || 'Falha ao salvar');
            }

            if (data.csrf) {
                csrfHash = data.csrf;
            }

            dirty = false;
            setStatus('Salvo em ' + (data.updated_at || ''), 'text-success');
        } catch (e) {
            setStatus('Erro ao salvar: ' + e.message, 'text-danger');
        } finally {
            saving = false;
        }
    };

    textarea.addEventListener('input', () => {
        dirty = true;
        setStatus('Alteracoes pendentes', 'text-muted');
    });

    saveButton.addEventListener('click', save);

    setInterval(() => {
        if (dirty) save();
    }, 5000);
})();
</script>
