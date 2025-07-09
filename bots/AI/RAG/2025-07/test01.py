from langchain.text_splitter import CharacterTextSplitter
from langchain.chains import RetrievalQA

# NOVOS IMPORTS
from langchain_community.document_loaders import TextLoader
from langchain_community.vectorstores import Chroma
from langchain_ollama.embeddings import OllamaEmbeddings
from langchain_ollama import ChatOllama

# Passo 1: Criar documento
with open("organizacao_conhecimento.txt", "w", encoding="utf-8") as f:
    f.write("""
    Organização do Conhecimento é um campo da Ciência da Informação que estuda os métodos de representação, estruturação e recuperação da informação.
    Ela abrange áreas como classificação, indexação, catalogação, taxonomias e ontologias. Seu objetivo é facilitar o acesso à informação,
    permitindo que usuários encontrem e compreendam conteúdos relevantes. A Organização do Conhecimento é fundamental em bibliotecas, arquivos, museus e bases digitais.
    """)

# Passo 2: Carregar e dividir
loader = TextLoader("organizacao_conhecimento.txt", encoding="utf-8")
docs = loader.load()
splitter = CharacterTextSplitter(chunk_size=300, chunk_overlap=50)
texts = splitter.split_documents(docs)

# Passo 3: Embeddings + Chroma
embeddings = OllamaEmbeddings(model="llama3.1")
db = Chroma.from_documents(texts, embeddings, persist_directory="./db_rag")
db.persist()

# Passo 4: Recuperação e QA
retriever = db.as_retriever(search_kwargs={"k": 2})
llm = ChatOllama(model="llama3.1")
qa = RetrievalQA.from_chain_type(llm=llm, retriever=retriever, return_source_documents=True)

# Passo 5: Pergunta
query = "O que é organização do conhecimento?"
resposta = qa.invoke({"query": query})

# Mostrar resposta
print("🔍 Resposta:", resposta["result"])

# Mostrar fontes (opcional)
for doc in resposta["source_documents"]:
    print("\n📄 Fonte:")
    print(doc.page_content.strip())
