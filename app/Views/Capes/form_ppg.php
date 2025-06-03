<div class="container">
    <div class="row">
        <div class="col-md-3">
            <h1>PPG Form</h1>
            <form>
                <label>Código do PPG</label>
                <br>
                <input type="text" name="ppg" value="<?= $ppg ?? '' ?>">
                <br>
                <input type="submit" value="Filtrar" class="btn btn-primary mt-2">
            </form>
        </div>
        <div class="col-md-9">
            <?= $sf; ?>
        </div>
    </div>
</div>