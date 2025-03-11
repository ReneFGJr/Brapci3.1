import os
from elasticsearch import Elasticsearch, helpers
from PyPDF2 import PdfReader
from sentence_transformers import SentenceTransformer
import requests
import json
from colorama import Fore, Style, init

# Inicializa o colorama para Windows
init(autoreset=True)

# Configurações do ElasticSearch
ELASTIC_URL = 'http://143.54.112.91:9200'
INDEX_NAME = 'pdf_documents'

# Inicialização do Elasticsearch
es = Elasticsearch(ELASTIC_URL)

# Criação do índice (se não existir)
if not es.indices.exists(index=INDEX_NAME):
    es.indices.create(index=INDEX_NAME)

# Função para extrair texto de PDFs
def extract_text_from_pdfs(pdf_folder):
    documents = []
    for filename in os.listdir(pdf_folder):
        if filename.endswith('.pdf'):
            pdf_path = os.path.join(pdf_folder, filename)
            reader = PdfReader(pdf_path)
            text = " ".join([page.extract_text() for page in reader.pages if page.extract_text()])
            documents.append({'filename': filename, 'content': text})
    return documents

# Função para indexar documentos no ElasticSearch
def index_documents(documents):
    actions = [
        {
            "_index": INDEX_NAME,
            "_source": doc
        } for doc in documents
    ]
    helpers.bulk(es, actions)

# Função para buscar documentos relevantes
def search_documents(query, top_k=3):
    response = es.search(
        index=INDEX_NAME,
        body={
            "query": {
                "match": {
                    "content": query
                }
            },
            "size": top_k
        }
    )
    return [hit['_source']['content'] for hit in response['hits']['hits']]

# Função para gerar resposta usando o OLLAMA API
def generate_response_with_ollama(context, question):
    prompt = f"Baseado nos seguintes documentos:\n{context}\n\nResponda à seguinte pergunta:\n{question}"

    response = requests.post(
        'http://localhost:11434/api/generate',
        headers={'Content-Type': 'application/json'},
        data=json.dumps({
            "model": "llama3.1",  # Certifique-se de que o modelo está correto
            "prompt": prompt,
            "stream": False  # Tente definir como False se o OLLAMA estiver retornando respostas tokenizadas
        })
    )

    try:
        print(Fore.CYAN + "Resposta bruta da API:", response.text)
        response_json = response.json()

        # Tentar pegar o campo correto da resposta
        if 'response' in response_json:
            return response_json['response']
        elif 'text' in response_json:
            return response_json['text']
        else:
            return f"Resposta inesperada: {response_json}"

    except json.JSONDecodeError as e:
        return f"Erro de decodificação JSON: {e}\nResposta bruta da API: {response.text}"


# Pipeline completo de RAG
def rag_pipeline(pdf_folder, question):
    # Extração e indexação
    documents = extract_text_from_pdfs(pdf_folder)
    index_documents(documents)

    # Recuperação de documentos relevantes
    retrieved_docs = search_documents(question)

    # Geração da resposta com OLLAMA
    context = "\n".join(retrieved_docs)
    response = generate_response_with_ollama(context, question)

    return retrieved_docs, response

# Uso do pipeline
pdf_folder_path = 'docs'
pergunta = 'O que é o VUFIND? Responda de forma breve, em uma linha.'
retrieved_docs, resposta = rag_pipeline(pdf_folder_path, pergunta)

# Exibição organizada
print(Fore.GREEN + "\n" + "="*50)
print(Fore.CYAN + "📚 **Documentos Utilizados na Resposta** 📚")
print(Fore.GREEN + "="*50)
for idx, doc in enumerate(retrieved_docs, 1):
    print(f"{idx}. {doc[:300]}...")  # Mostra os primeiros 300 caracteres de cada documento

print(Fore.GREEN + "\n" + "="*50)
print(Fore.CYAN + "📄 **Resposta Gerada com Base nos Documentos PDF** 📄")
print(Fore.GREEN + "="*50)
print(Fore.YELLOW + resposta.strip())
print(Fore.GREEN + "="*50)
