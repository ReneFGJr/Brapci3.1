"""Atualiza o Modelfile do BrapciBotIA e recria o modelo no Ollama."""

from __future__ import annotations

import json
import shutil
import subprocess
from html.parser import HTMLParser
from pathlib import Path
from typing import Any
from urllib.error import HTTPError, URLError
from urllib.request import Request, urlopen


STATISTICS_URL = "https://cip.brapci.inf.br/api/brapci/statistics"
BENANCIB_URL = "https://cip.brapci.inf.br/api/page/benancib"
JOURNALS_URL = "https://cip.brapci.inf.br/api/brapci/source/journal"
EVENTS_URL = "https://cip.brapci.inf.br/api/brapci/source/E"
SCRIPT_DIR = Path(__file__).resolve().parent
MODEL_TEMPLATE = SCRIPT_DIR / "Modelfile.model"
MODELFILE = SCRIPT_DIR / "Modelfile"
OLLAMA_MODEL = "brapci-ia"


class _HTMLTextExtractor(HTMLParser):
    """Converte os fragmentos HTML da API em texto simples."""

    BLOCK_TAGS = {"br", "div", "h1", "h2", "h3", "li", "p"}

    def __init__(self) -> None:
        super().__init__()
        self.parts: list[str] = []

    def handle_starttag(self, tag: str, attrs: list[tuple[str, str | None]]) -> None:
        if tag in self.BLOCK_TAGS:
            self.parts.append("\n")

    def handle_endtag(self, tag: str) -> None:
        if tag in self.BLOCK_TAGS:
            self.parts.append("\n")

    def handle_data(self, data: str) -> None:
        self.parts.append(data)

    def text(self) -> str:
        lines = (" ".join(line.split()) for line in "".join(self.parts).splitlines())
        return "\n\n".join(line for line in lines if line)


def fetch_json(url: str, timeout: int = 30) -> Any:
    """Consulta um endpoint JSON da BRAPCI."""
    request = Request(url, headers={"Accept": "application/json"})

    try:
        with urlopen(request, timeout=timeout) as response:
            return json.load(response)
    except (HTTPError, URLError, TimeoutError, json.JSONDecodeError) as exc:
        raise RuntimeError(f"Nao foi possivel consultar {url}: {exc}") from exc


def fetch_statistics(url: str = STATISTICS_URL, timeout: int = 30) -> str:
    """Consulta a API e devolve as estatisticas formatadas para o Modelfile."""
    payload = fetch_json(url, timeout)
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


def fetch_benancib(url: str = BENANCIB_URL, timeout: int = 30) -> str:
    """Consulta e converte a pagina do BENANCIB em texto simples."""
    payload = fetch_json(url, timeout)
    if not isinstance(payload, list):
        raise RuntimeError("Resposta invalida da API do BENANCIB.")

    parser = _HTMLTextExtractor()
    for item in payload:
        if not isinstance(item, dict) or not isinstance(item.get("row"), str):
            raise RuntimeError("Item invalido na resposta da API do BENANCIB.")
        parser.feed(item["row"])

    content = parser.text()
    if not content:
        raise RuntimeError("A API do BENANCIB nao retornou conteudo textual.")
    return content


def format_sources(payload: Any, collection: str, description: str) -> str:
    """Filtra e formata fontes bibliograficas de uma colecao."""
    if not isinstance(payload, list):
        raise RuntimeError(f"Resposta invalida da API de {description}.")

    lines = []
    for item in payload:
        if not isinstance(item, dict) or item.get("jnl_collection") != collection:
            continue
        name = str(item.get("jnl_name") or "").strip()
        if not name:
            continue
        details = []
        abbreviation = str(item.get("jnl_name_abrev") or "").strip()
        issn = str(item.get("jnl_issn") or "").strip()
        eissn = str(item.get("jnl_eissn") or "").strip()
        start = str(item.get("jnl_ano_inicio") or "").strip()
        end = str(item.get("jnl_ano_final") or "").strip()
        if abbreviation and abbreviation.casefold() != name.casefold():
            details.append(f"sigla: {abbreviation}")
        if issn:
            details.append(f"ISSN: {issn}")
        if eissn:
            details.append(f"eISSN: {eissn}")
        if start:
            details.append(f"periodo: {start}-{end if end and end != '0' else 'atual'}")
        suffix = f" ({'; '.join(details)})" if details else ""
        lines.append(f"- {name}{suffix}")

    if not lines:
        raise RuntimeError(f"Nenhuma fonte encontrada para {description}.")
    return "\n".join(lines)


def fetch_brazilian_journals(url: str = JOURNALS_URL, timeout: int = 30) -> str:
    """Consulta e formata as revistas brasileiras (colecao JA)."""
    return format_sources(fetch_json(url, timeout), "JA", "revistas brasileiras")


def fetch_international_journals(url: str = JOURNALS_URL, timeout: int = 30) -> str:
    """Consulta e formata as revistas internacionais (colecao JE)."""
    return format_sources(fetch_json(url, timeout), "JE", "revistas internacionais")


def fetch_events(url: str = EVENTS_URL, timeout: int = 30) -> str:
    """Consulta e formata os eventos (colecao EV)."""
    return format_sources(fetch_json(url, timeout), "EV", "eventos")


def create_modelfile(
    template_path: Path = MODEL_TEMPLATE,
    output_path: Path = MODELFILE,
) -> Path:
    """Cria o Modelfile preenchendo todos os marcadores com dados das APIs."""
    content = template_path.read_text(encoding="utf-8")
    fetchers = {
        "{statistics}": fetch_statistics,
        "{BENANCIB}": fetch_benancib,
        "{REVISTAS_BRASILEIRAS}": fetch_brazilian_journals,
        "{REVISTAS_INTERNACIONAIS}": fetch_international_journals,
        "{EVENTOS}": fetch_events,
    }
    missing = [placeholder for placeholder in fetchers if placeholder not in content]
    if missing:
        raise RuntimeError(
            f"Marcadores nao encontrados em {template_path}: {', '.join(missing)}"
        )
    for placeholder, fetcher in fetchers.items():
        content = content.replace(placeholder, fetcher())

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
