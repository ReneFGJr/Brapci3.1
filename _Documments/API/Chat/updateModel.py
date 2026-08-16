"""Atualiza o Modelfile do BrapciBotIA e recria o modelo no Ollama."""

from __future__ import annotations

import json
import shutil
import subprocess
from pathlib import Path
from typing import Any
from urllib.error import HTTPError, URLError
from urllib.request import Request, urlopen


STATISTICS_URL = "https://cip.brapci.inf.br/api/brapci/statistics"
SCRIPT_DIR = Path(__file__).resolve().parent
MODEL_TEMPLATE = SCRIPT_DIR / "Modelfile.model"
MODELFILE = SCRIPT_DIR / "Modelfile"
OLLAMA_MODEL = "brapcibotia"


def fetch_statistics(url: str = STATISTICS_URL, timeout: int = 30) -> str:
    """Consulta a API e devolve as estatisticas formatadas para o Modelfile."""
    request = Request(url, headers={"Accept": "application/json"})

    try:
        with urlopen(request, timeout=timeout) as response:
            payload: Any = json.load(response)
    except (HTTPError, URLError, TimeoutError, json.JSONDecodeError) as exc:
        raise RuntimeError(f"Nao foi possivel consultar {url}: {exc}") from exc

    if not isinstance(payload, dict) or not isinstance(payload.get("data"), list):
        raise RuntimeError("Resposta invalida da API de estatisticas.")

    lines = []
    for item in payload["data"]:
        if not isinstance(item, dict) or "name" not in item or "total" not in item:
            raise RuntimeError("Item invalido na resposta da API de estatisticas.")
        lines.append(f"- {item['name']}: {item['total']}")

    if payload.get("update"):
        lines.extend(("", f"Dados atualizados em: {payload['update']}"))

    return "\n".join(lines)


def create_modelfile(
    template_path: Path = MODEL_TEMPLATE,
    output_path: Path = MODELFILE,
) -> Path:
    """Cria o Modelfile substituindo {statistics} pelos dados atuais da API."""
    template = template_path.read_text(encoding="utf-8")
    placeholder = "{statistics}"
    if placeholder not in template:
        raise RuntimeError(f"Marcador {placeholder} nao encontrado em {template_path}.")

    content = template.replace(placeholder, fetch_statistics())
    output_path.write_text(content, encoding="utf-8")
    return output_path


def recreate_ollama_model(
    model_name: str = OLLAMA_MODEL,
    modelfile_path: Path = MODELFILE,
) -> None:
    """Remove o modelo existente e o recria usando o Modelfile informado."""
    ollama = shutil.which("ollama")
    if ollama is None:
        raise RuntimeError("Executavel 'ollama' nao encontrado no PATH.")
    if not modelfile_path.is_file():
        raise RuntimeError(f"Modelfile nao encontrado: {modelfile_path}")

    existing_models = subprocess.run(
        [ollama, "list"],
        check=True,
        capture_output=True,
        text=True,
    ).stdout.splitlines()[1:]
    if any(line.split()[0].split(":", 1)[0].lower() == model_name.lower() for line in existing_models if line.split()):
        subprocess.run([ollama, "rm", model_name], check=True)

    subprocess.run(
        [ollama, "create", model_name, "-f", str(modelfile_path)],
        check=True,
    )


def main() -> None:
    modelfile = create_modelfile()
    print(f"Modelfile criado: {modelfile}")
    recreate_ollama_model(modelfile_path=modelfile)
    print(f"Modelo Ollama recriado: {OLLAMA_MODEL}")


if __name__ == "__main__":
    main()
