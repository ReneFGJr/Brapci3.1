<h4>Citações BrapciLabs</h4>
<?php
$cited = $data['cited'] ?? array();
$without = $data['withoutCited'] ?? array();

if (count($without) > 0) {
	echo '<div class="alert alert-warning" role="alert">';
	echo '<strong>Total ' . count($without) . ' registros sem referência encontrada:</strong> ';
	echo '<i class="bi bi-exclamation-triangle-fill"></i>';
		foreach ($without as $item) {
		$link = base_url('/v/' . $item);
		echo '<a href="' . esc($link) . '" target="_blank">' . esc($item) . '</a> ';
	}
	echo '</div>';
}


if (!is_array($cited) or count($cited) == 0) {
	echo '<p class="text-muted">Sem referências.</p>';
} else {
	$groups = array();

	foreach ($cited as $item) {
		$rdf = '0';
		if (is_array($item)) {
			$rdf = (string)($item['ca_rdf'] ?? '0');
			if (!isset($groups[$rdf])) {
				$groups[$rdf] = array();
			}
			array_push($groups[$rdf], $item);
		}
	}

	if (count($groups) == 0) {
		echo '<p class="text-muted">Sem referências.</p>';
	} else {
		foreach ($groups as $rdf => $refs) {
            $groupId = 'cited_group_' . preg_replace('/[^0-9A-Za-z_]/', '_', (string)$rdf);
            $href = '<a href="' . base_url('/v/' . $rdf) . '" target="_blank">';
            $hrefa = '</a>';
			echo '<h5 class="mt-3 mb-1 d-flex align-items-center gap-2">';
			echo '<span>ca_rdf: ' . $href . esc($rdf) . $hrefa . '</span>';
			echo '<button type="button" class="btn btn-outline-secondary btn-sm py-0 px-2" data-target="' . esc($groupId) . '" data-rdf="' . esc($rdf) . '" data-locked="0" onclick="toggleCitedGroupLock(this)" title="Travar/Destravar registros" aria-label="Travar/Destravar registros"><i class="bi bi-unlock"></i></button>';
			echo '</h5>';
			echo '<div id="' . esc($groupId) . '" class="table-responsive cited-group">';
			echo '<table class="table table-sm table-striped table-hover mb-3">';
			echo '<thead><tr>';
			echo '<th style="width: 60px;">#</th>';
			echo '<th>ca_text</th>';
			echo '<th style="width: 110px;">ca_year</th>';
			echo '<th>ca_doi</th>';
			echo '<th style="width: 120px;">action</th>';
			echo '</tr></thead><tbody>';

			$nr = 0;
            $ida = 0;
			foreach ($refs as $row) {
				$nr++;
				$caText = '';
				$caYear = '';
				$caDoi = '';
				$action = '-';

				if (is_array($row)) {
					// Skip se estiver bloqueado
					$caBlocked = (int)($row['ca_blocked'] ?? 0);
					if ($caBlocked === 1) {
						continue;
					}

					$caText = trim((string)($row['ca_text'] ?? ''));
					$caYear = trim((string)($row['ca_year'] ?? ''));
					$caDoi = trim((string)($row['ca_doi'] ?? ''));
					$idCa = (int)($row['id_ca'] ?? 0);
					$caTipo = (int)($row['ca_tipo'] ?? 0);
					if ($idCa > 0) {
						$urlEdit = base_url('/labs/cited/edit/' . $idCa);
						$jsUrlEdit = str_replace("'", "\\'", $urlEdit);
						$urlJoin = base_url('api/brapci/cited/join');

						$action = '<nobr>';
                        $action .= '<button type="button" class="btn btn-outline-danger btn-sm" data-id="' . $idCa . '" onclick="deleteCitedRecord(this)" title="Deletar" aria-label="Deletar">&#128465;</button>';
						$action .= '<button type="button" class="btn btn-outline-primary btn-sm" onclick="const w = window.open(\'' . $jsUrlEdit . '\',\'newwin\',\'scrollbars=no,resizable=yes,width=800,height=600,top=10,left=10\'); if (w) { w.focus(); } return false;" title="Editar" aria-label="Editar">&#9998;</button> ';
                        if ($ida > 0) {
							$action .= '<button type="button" class="btn btn-outline-secondary btn-sm" data-id="' . $idCa . '" data-prev-id="' . $ida . '" data-url="' . esc($urlJoin) . '" onclick="joinCitedRecord(this)" title="JOIN com anterior" aria-label="JOIN com anterior">&#128279;</button> ';
                        }
                        $action .= '</nobr>';
					}
                    $ida = $idCa;
				} else {
					$caText = trim((string)$row);
				}

				echo '<tr>';
				echo '<td>' . $nr . '</td>';
				echo '<td>' . esc($caText) . '</td>';
				echo '<td>' . esc($caYear) . '</td>';
				echo '<td>' . esc($caDoi) . '</td>';
				echo '<td class="cited-action-cell">' . $action . '</td>';
				echo '</tr>';
			}

			echo '</tbody></table>';
			echo '</div>';
		}

		echo '<style>';
		echo '.cited-group-locked { opacity: 0.85; }';
		echo '.cited-group-hidden { display: none; }';
		echo '.cited-group-locked .cited-action-cell a, .cited-group-locked .cited-action-cell button { pointer-events: none; opacity: 0.45; }';
		echo '</style>';

		echo '<script>';
		echo 'function toggleCitedGroupLock(btn) {';
		echo '  const targetId = btn.getAttribute("data-target");';
		echo '  const rdfId = btn.getAttribute("data-rdf");';
		echo '  const group = document.getElementById(targetId);';
		echo '  if (!group) { return; }';
		echo '  const isLocked = btn.getAttribute("data-locked") === "1";';
		echo '  const lockNow = !isLocked;';
		echo '  const endpoint = lockNow ? "/api/brapci/citedLock" : "/api/brapci/citedUnLock";';
		echo '  btn.disabled = true;';
		echo '  fetch(endpoint + "?idz=" + rdfId)';
		echo '    .then(response => response.json())';
		echo '    .then(data => {';
		echo '      if (data.status === "200") {';
		echo '        btn.setAttribute("data-locked", lockNow ? "1" : "0");';
		echo '        btn.innerHTML = lockNow ? "<i class=\"bi bi-lock\"></i>" : "<i class=\"bi bi-unlock\"></i>";';
		echo '        btn.classList.toggle("btn-outline-danger", lockNow);';
		echo '        btn.classList.toggle("btn-outline-secondary", !lockNow);';
		echo '        group.classList.toggle("cited-group-locked", lockNow);';
		echo '        group.classList.toggle("cited-group-hidden", lockNow);';
		echo '      } else {';
		echo '        alert("Erro ao atualizar: " + data.message);';
		echo '      }';
		echo '      btn.disabled = false;';
		echo '    })';
		echo '    .catch(error => {';
		echo '      alert("Erro na requisição: " + error.message);';
		echo '      btn.disabled = false;';
		echo '    });';
		echo '}';
		echo '';
		echo 'function deleteCitedRecord(btn) {';
		echo '  if (!confirm("Confirma exclusão desta referência " + btn.getAttribute("data-id") + "?")) { return; }';
		echo '  const recordId = btn.getAttribute("data-id");';
		echo '  btn.disabled = true;';
		echo '  url = "'.base_url('/api/brapci/citedDelete?idz=') . '" + recordId;';
		echo '  fetch(url, { method: "GET" })';
		echo '    .then(response => response.json())';
		echo '    .then(data => {';
		echo '      if (data.status === "200") {';
		echo '        const row = btn.closest("tr");';
		echo '        if (row) {';
		echo '          row.style.transition = "opacity 0.3s ease";';
		echo '          row.style.opacity = "0";';
		echo '          setTimeout(() => row.remove(), 300);';
		echo '        }';
		echo '      } else {';
		echo '        alert("Erro ao deletar: " + data.message);';
		echo '        btn.disabled = false;';
		echo '      }';
		echo '    })';
		echo '    .catch(error => {';
		echo '      alert("Erro na requisição: " + error.message);';
		echo '      btn.disabled = false;';
		echo '    });';
		echo '}';
		echo '';
		echo 'function joinCitedRecord(btn) {';
		echo '  const idz = btn.getAttribute("data-id");';
		echo '  const ida = btn.getAttribute("data-prev-id");';
		echo '  const baseUrl = btn.getAttribute("data-url");';
		echo '  if (!idz || !ida || !baseUrl) { return; }';
		echo '  btn.disabled = true;';
		echo '  const sep = baseUrl.indexOf("?") >= 0 ? "&" : "?";';
		echo '  const url = baseUrl + sep + "idz=" + encodeURIComponent(idz) + "&ida=" + encodeURIComponent(ida);';
		echo '  fetch(url, { method: "GET" })';
		echo '    .then(response => response.json())';
		echo '    .then(data => {';
		echo '      if (data.status === "200") {';
		echo '        const row = btn.closest("tr");';
		echo '        if (row) {';
		echo '          row.style.transition = "opacity 0.3s ease";';
		echo '          row.style.opacity = "0";';
		echo '          setTimeout(() => row.remove(), 300);';
		echo '        }';
		echo '      } else {';
		echo '        alert("Erro ao unir: " + (data.message || "resposta inválida"));';
		echo '        btn.disabled = false;';
		echo '      }';
		echo '    })';
		echo '    .catch(error => {';
		echo '      alert("Erro na requisição: " + error.message);';
		echo '      btn.disabled = false;';
		echo '    });';
		echo '}';
		echo '</script>';
	}
}