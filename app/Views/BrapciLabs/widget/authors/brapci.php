<!-- Nav tabs -->
<ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true">Resumo</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false">Artigos</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="messages-tab" data-bs-toggle="tab" data-bs-target="#messages" type="button" role="tab" aria-controls="messages" aria-selected="false">Livros</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="settings-tab" data-bs-toggle="tab" data-bs-target="#settings" type="button" role="tab" aria-controls="settings" aria-selected="false">Eventos</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="citations-tab" data-bs-toggle="tab" data-bs-target="#citations" type="button" role="tab" aria-controls="citations" aria-selected="false">Citações</button>
    </li>
</ul>

<!-- Tab panes -->
<div class="tab-content card">
    <div class="tab-pane active" id="home" role="tabpanel" aria-labelledby="home-tab" tabindex="0">.x1..</div>
    <div class="tab-pane" id="profile" role="tabpanel" aria-labelledby="profile-tab" tabindex="0">.x2..</div>
    <div class="tab-pane" id="messages" role="tabpanel" aria-labelledby="messages-tab" tabindex="0">.x3..</div>
    <div class="tab-pane" id="settings" role="tabpanel" aria-labelledby="settings-tab" tabindex="0">
        <?= view('BrapciLabs/widget/authors/brapci_details_proceeding') ?>
    </div>
    <div class="tab-pane p-3" id="citations" role="tabpanel" aria-labelledby="citations-tab" tabindex="0">
        <?= view('BrapciLabs/widget/authors/brapci_details_cited') ?>
    </div>
</div>

<script>
    const triggerTabList = document.querySelectorAll('#myTab button')
    triggerTabList.forEach(triggerEl => {
        const tabTrigger = new bootstrap.Tab(triggerEl)

        triggerEl.addEventListener('click', event => {
            event.preventDefault()
            tabTrigger.show()
        })
    })
</script>