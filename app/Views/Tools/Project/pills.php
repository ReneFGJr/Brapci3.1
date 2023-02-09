<ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#pills-home" type="button" role="tab" aria-controls="pills-home" aria-selected="true"><?= $title[0]; ?></button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false"><?= $title[1]; ?></button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="pills-contact-tab" data-bs-toggle="pill" data-bs-target="#pills-contact" type="button" role="tab" aria-controls="pills-contact" aria-selected="false"><?= $title[2]; ?></button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="pills-export-tab" data-bs-toggle="pill" data-bs-target="#pills-export" type="button" role="tab" aria-controls="pills-export" aria-selected="false" ><?= $title[3]; ?></button>
    </li>
</ul>
<div class="tab-content" id="pills-tabContent">
    <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab" tabindex="0"><?= $tab[0]; ?></div>
    <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab" tabindex="0"><?= $tab[1]; ?></div>
    <div class="tab-pane fade" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab" tabindex="0"><?= $tab[2]; ?></div>
    <div class="tab-pane fade" id="pills-export" role="tabpanel" aria-labelledby="pills-export-tab" tabindex="0"><?= $tab[3]; ?></div>
</div>