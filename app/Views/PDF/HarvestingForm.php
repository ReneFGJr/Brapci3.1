<?php

$action = PATH . '/pdfHarvesting';

$sx = '';
$sx .= '<h2 class="mb-3">Informar IDs para Harvesting PDF</h2>';
$sx .= '<form method="get" action="' . $action . '">';
$sx .= '  <div class="mb-3">';
$sx .= '      <label for="IDs" class="form-label">IDs (separados por vírgula)</label>';
$sx .= '      <textarea id="IDs" name="IDs" class="form-control" rows="6" placeholder="123,456,789" required></textarea>';
$sx .= '      <div class="form-text">Exemplo: 123,456,789</div>';
$sx .= '  </div>';
$sx .= '  <button type="submit" class="btn btn-primary">Enviar</button>';
$sx .= '</form>';

echo bs(bsc($sx, 12));
