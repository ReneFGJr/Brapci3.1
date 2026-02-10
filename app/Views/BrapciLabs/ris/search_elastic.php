<h6>Busca na base</h6>
<form method="post" action="/labs/works/search">
    <textarea name="q" id="q" rows="5" class="form-control border border-secondary" placeholder="Insira a estratégia de busca"></textarea>
    <div>
        <input type="radio" checked id="type" name="type" value="ris" class="me-1">Base local
        <input type="radio" id="type" name="type" value="brapci" class="ms-4 me-1">Na Brapci
        <input type="radio" id="type" name="type" value="smart" class="ms-4 me-1">Smart Retriavel <sup>(<i>beta</i>)</sup>
    </div>
    <button class="btn btn-primary mt-2" style="width: 100%;" id="search-btn">Search</button>
</form>