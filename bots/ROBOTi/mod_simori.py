import database
import requests
import os, time, sys

# models.py (exemplo)

class RepositorioModel:
    def find_by_id(self, repo_id):
        return database.query("SELECT * FROM simori.repository WHERE id_rp = %s", (repo_id,))


class OaiRecordModel:
    def find_all(self, repository, status, deleted, harvesting, xml_not_null=False):
        sql = """SELECT * FROM simori.oai_records
                 WHERE repository = %s AND status = %s AND deleted = %s
                 AND harvesting = %s"""
        if xml_not_null:
            sql += " AND xml IS NOT NULL"

        return database.query(sql, (repository, status, deleted, harvesting))

    def update(self, record_id, data):
        print(data)
        database.update2("simori.oai_records", data, where="id = %s", params=(record_id,))


class OaiSetModel:
    def find_by_spec(self, repo_id, set_spec):
        sql = "SELECT * FROM simori.oai_sets WHERE identify_id = %s AND set_spec = %s"
        return database.query(sql, (repo_id, set_spec))


class OaiTriplesModel:
    def extract_triples(self, record, set_spec_id, repo_id):
        # implementação da extração de triples (a partir do XML, por ex.)
        print(f"Extraindo triples de {record[1]}...")

def simori_main():
    query = "SELECT repository, count(*) as total FROM simori.oai_records WHERE deleted = 0 AND harvesting = 0 group by repository"
    repositories = database.query(query)

    for repo in repositories:
        repo_id = repo[0]
        total = repo[1]
        print(f"Repositório ID: {repo_id}, Registros pendentes: {total}")
        simori_harvesting(repo_id)
        #simori_triples(repo_id)

def simori_triples(repo_id):
    """
    Realiza a extração de metadados (triples) para um repositório específico.
    """
    repo_model = RepositorioModel()
    record_model = OaiRecordModel()
    triples_model = OaiTriplesModel()
    set_model = OaiSetModel()

    # 🔹 Recupera informações do repositório
    repo = repo_model.find_by_id(repo_id)
    if not repo:
        return "Repositório não encontrado."

    # 🔹 Seleciona registros válidos para processamento
    records = record_model.find_all(
        repository=repo_id,
        status=1,
        deleted=0,
        harvesting=1,
        xml_not_null=True
    )

    total = len(records)
    output = []

    # 🔹 Loop principal: processa cada registro
    for i, record in enumerate(records, start=1):
        msg = f"Processando registro ID {i}/{total}<br>"
        print(f"[{i}/{total}] Iniciando extração...")

        identifier = (record[2]).strip()
        set_spec_name = record[4].strip()
        print(f"Identificador: {identifier}, setSpec: {set_spec_name}")

        if not identifier:
            continue

        set_spec_row = set_model.find_by_spec(repo_id, set_spec_name)
        print(f"setSpec Row: {set_spec_row}")  # Debug: exibe a linha do setSpec
        set_spec_id = set_spec_row[0][0] if set_spec_row else None

        msg += f"🔹 Processando: <code>{identifier}</code><br>"
        msg += f"setSpec: <code>{set_spec_name}</code> (ID: {set_spec_id})<br>"

        # 🔹 Extrai triples
        triples_model.extract_triples(record, set_spec_id, repo_id)

        # 🔹 Atualiza status do registro
        record_model.update(record[0], {"harvesting": 2})

        msg += f"✅ Extração concluída para <b>{identifier}</b><br><br>"
        output.append(msg)

    # 🔹 Finalização
    final_msg = "🏁 Extração finalizada para o repositório."
    # Retorna HTML consolidado (para exibição via Flask ou Jinja)
    return final_msg

def get_register(base_url: str, identifier: str, record: dict, retries: int = 3, delay: int = 2):
    """
    Baixa o XML de um registro OAI-PMH com tratamento de erros HTTP, SSL e tempo limite.
    """
    # 🔹 Remove www. se causar erro de certificado (como o da FURG)
    base_url = base_url.replace("://www.", "://")

    # 🔹 Monta a URL final
    url = f"{base_url}?verb=GetRecord&metadataPrefix=oai_dc&identifier={identifier}"

    attempt = 0
    while attempt < retries:
        try:
            response = requests.get(url, timeout=30, verify=False)  # SSL ignorado apenas para compatibilidade
            if response.status_code == 200:
                return response.text
            else:
                print(f"⚠️ HTTP {response.status_code} para {identifier} (tentativa {attempt+1}/{retries})")
                # Repositórios DSpace antigos às vezes retornam 500 para coleções "ri/"
                if response.status_code == 500 and '/ri/' in identifier:
                    print(f"⏭️ Pulando coleção: {identifier}")
                    return None
        except requests.exceptions.RequestException as e:
            print(f"⚠️ Erro na requisição ({type(e).__name__}): {e} (tentativa {attempt+1}/{retries})")

        attempt += 1
        time.sleep(delay)

    # 🔹 Após todas as tentativas, salva log e retorna None
    log_msg = f"[{time.strftime('%Y-%m-%d %H:%M:%S')}] Falha ao coletar {identifier} ({url})\n"
    with open("logs/oai_errors.log", "a", encoding="utf-8") as f:
        f.write(log_msg)
    return None

def simori_harvesting(repo_id: int):
    repo_model = RepositorioModel()
    record_model = OaiRecordModel()
    triples_model = OaiTriplesModel()
    set_model = OaiSetModel()

    """
    Coleta os registros de um repositório OAI e salva o XML de cada registro.
    """

    # 🔹 Recupera informações do repositório
    repo = repo_model.find_by_id(repo_id)
    if not repo:
        return "Repositório não encontrado."

    if not repo:
        print("❌ Repositório não encontrado.")
        return "Repositório não encontrado."

    # 🔹 Busca registros ainda não coletados
    # 🔹 Seleciona registros válidos para processamento
    records = record_model.find_all(
        repository=repo_id,
        status=0,
        deleted=0,
        harvesting=0,
        xml_not_null=True
    )

    total = len(records)

    if not records:
        print("ℹ️ Nenhum registro pendente de coleta.")
        return "Nenhum registro pendente de coleta."
    repo = repo[0]
    oai_url = repo[4].rstrip("/")
    print(f"🌐 Iniciando coleta no repositório: {repo[1]} ({oai_url})")
    print(f"🔸 Total de registros a coletar: {len(records)}\n")
    processed = 0
    # 🔹 3. Para cada registro, baixa o XML via GetRecord
    for idx, record in enumerate(records, start=1):
        identifier = (record[2] or "").strip()
        processed += 1
        if processed % 10000 == 0:
            return f"Processados {processed} de {total} registros..."
        if not identifier:
            continue

        print(f"🔹 [{idx}/{len(records)}] Coletando: {identifier}")
        try:
            xml = get_register(oai_url, identifier, record)
            # xml = xml.replace("//", "") if xml else None
            query = "update simori.oai_records set xml = '"+xml+"', harvesting = 1, status = 1 where id = "+str(record[0])
            print(query)
            database.query(query)
            print("--------------")
            
            #database.update2(
            #    "simori.oai_records",
            #    {"xml": xml, "harvesting": 1, "status": 1},
            #    where="id = %s",
            #    params=(record[0],)
            #)
        except Exception as e:
            print(f"⚠️ Erro ao coletar {identifier}: {e}\n")
            #sys.exit()

        # Delay opcional para não sobrecarregar o servidor remoto
        # time.sleep(0.5)  # 0.5 segundos

    print("🏁 Coleta finalizada para o repositório.")
    print(f"🔙 Retorne à página: /repository/show/{repo_id}")
    return "Coleta concluída."

if __name__ == "__main__":
    # Exemplo de execução direta
    repo_id = 89 # ID do repositório a ser processado
    result_html = simori_harvesting(repo_id)
    result_html = result_html + simori_triples(repo_id)
    print(result_html)  # Ou renderizar via Flask/Jinja conforme necessário