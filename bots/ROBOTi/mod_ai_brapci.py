import requests

def chat(message):
    # URL da API que você quer consultar
    url = "https://cip.brapci.inf.br/api/ai/chat"

    params = {
        "message": 'Com base nas categorias: Identifique o tipo dessa fonte e o ano da publicação, os tipos são: "Não Identificado" como "NI"; "Artigos" como "ARTICLE"; "Livro" como "BOOK"; "Cap. Livro" como "BOOK.CAP"; "Anais de eventos" como "PROCEEDINGS"; "Tese" como "THESE"; "Dissertação" como "DISSERTATION"; "TCC" como "TCC"; "Link de internet" como "LINK"; "Journal Diário" como "NEWSPAPPER"; "Filme" como "MOVIE"; "Revista semanal (Entreterimento)" como "MAGAZINE"; "Leis" como "LAW"; "Relatórios" como "REPORT"; "Normas técnicas" como "STANDART"; "Entrevista" como "INTERVIEW"; "Software" como "SOFTWARE"; "Patentes" como "PATENT"; "Base de dados" como "DATABASE"; "Notas de Pesquisa / Outros" como "OTHER"; "Nulo - Null" como "NULL";  Responda apenas o tipo, ponto e virgula e o ano, , informe número do grau de certeza de 0 a 9 entre colchetes, sendo o 0 menos confiável. A referência é SILVEIRA, M. A. A.; CAREGNATO, S. E. Demarcações epistemológicas dos estudos de citação: o fenômeno da citação (2000-2010). Informação & Sociedade: Estudos, v. 27, n. 3, 2017.'
    }

    # Envie a requisição GET
    response = requests.get(url, params=params)

    # Verifique o status da resposta
    if response.status_code == 200:
        # Se a requisição for bem-sucedida, processar o JSON retornado
        data = response.json()
        print(data)
    else:
        # Caso haja um erro, exibir o status code
        print(f"Erro: {response.status_code}")