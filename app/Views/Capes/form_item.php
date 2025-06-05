<form method="post">
    <h4>Aderência</h4>
    <input type="radio" name="C1" value="24" <?= isset($C1) && $C1 == 24 ? 'checked' : '' ?>> Muito bom
    <br><input type="radio" name="C1" value="18" <?= isset($C1) && $C1 == 18 ? 'checked' : '' ?>> Bom
    <br><input type="radio" name="C1" value="12" <?= isset($C1) && $C1 == 12 ? 'checked' : '' ?>> Regular
    <br><input type="radio" name="C1" value="6" <?= isset($C1) && $C1 == 6 ? 'checked' : '' ?>> Fraco
    <br><input type="radio" name="C1" value="0" <?= isset($C1) && $C1 == 0 ? 'checked' : '' ?>> Inexistente
    <br>

    <h4>Premiação e/ou financiamento</h4>
    <input type="radio" name="C2" value="10" <?= isset($C2) && $C2 == 10 ? 'checked' : '' ?>> Muito bom
    <br><input type="radio" name="C2" value="7.5" <?= isset($C2) && $C2 == 7.5 ? 'checked' : '' ?>> Bom
    <br><input type="radio" name="C2" value="5" <?= isset($C2) && $C2 == 5 ? 'checked' : '' ?>> Regular
    <br><input type="radio" name="C2" value="2.5" <?= isset($C2) && $C2 == 2.5 ? 'checked' : '' ?>> Fraco
    <br><input type="radio" name="C2" value="0" <?= isset($C2) && $C2 == 0 ? 'checked' : '' ?>> Inexistente
    <br>

    <h4>Aplicabilidade</h4>
    <input type="radio" name="C3" value="10" <?= isset($C3) && $C3 == 6 ? 'checked' : '' ?>> SIM
    <br><input type="radio" name="C3" value="0" <?= isset($C3) && $C3 == 0 ? 'checked' : '' ?>> NÃO

    <h4>Capacitação de pessoas</h4>
    <input type="radio" name="C4" value="24" <?= isset($C4) && $C4 == 24 ? 'checked' : '' ?>> Muito bom
    <br><input type="radio" name="C4" value="18" <?= isset($C4) && $C4 == 18 ? 'checked' : '' ?>> Bom
    <br><input type="radio" name="C4" value="12" <?= isset($C4) && $C4 == 12 ? 'checked' : '' ?>> Regular
    <br><input type="radio" name="C4" value="6" <?= isset($C4) && $C4 == 6 ? 'checked' : '' ?>> Fraco
    <br><input type="radio" name="C4" value="0" <?= isset($C4) && $C4 == 0 ? 'checked' : '' ?>> Inexistente
    <br>

    <h4>Teor inovativo</h4>
    <input type="radio" name="C5" value="24" <?= isset($C5) && $C5 == 24 ? 'checked' : '' ?>> Muito bom
    <br><input type="radio" name="C5" value="18" <?= isset($C5) && $C5 == 18 ? 'checked' : '' ?>> Bom
    <br><input type="radio" name="C5" value="12" <?= isset($C5) && $C5 == 12 ? 'checked' : '' ?>> Regular
    <br><input type="radio" name="C5" value="6" <?= isset($C5) && $C5 == 6 ? 'checked' : '' ?>> Fraco
    <br><input type="radio" name="C5" value="0" <?= isset($C5) && $C5 == 0 ? 'checked' : '' ?>> Inexistente
    <br>

    <h4>Visibilidade</h4>
    <input type="radio" name="C6" value="12" <?= isset($C6) && $C6 == 12 ? 'checked' : '' ?>> Muito bom
    <br><input type="radio" name="C6" value="6" <?= isset($C6) && $C6 == 6 ? 'checked' : '' ?>> Bom
    <br><input type="radio" name="C6" value="3" <?= isset($C6) && $C6 == 3 ? 'checked' : '' ?>> Regular
    <br><input type="radio" name="C6" value="0" <?= isset($C6) && $C6 == 0 ? 'checked' : '' ?>> Inexistente
    <br>
    <input type="hidden" name="action" value="save">
    <input type="hidden" name="id" value="<?= isset($ID) ? $ID : '' ?>">
    <input type="hidden" name="ppg" value="<?= isset($ppg) ? $ppg : '' ?>">
    <input type="submit" class="btn btn-primary" value="Salvar">
</form>