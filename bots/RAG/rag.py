import os
from pathlib import Path

# LangChain (núcleo + integrações)
from langchain.text_splitter import RecursiveCharacterTextSplitter
from langchain_community.document_loaders import DirectoryLoader, PyPDFLoader, TextLoader
from langchain_community.vectorstores import Chroma
from langchain_core.prompts import ChatPromptTemplate
from langchain_core.runnables import RunnablePassthrough
from langchain_core.output_parsers import StrOutputParser

# Ollama (via langchain-ollama)
from langchain_ollama import ChatOllama, OllamaEmbeddings

# ----------------------------
# CONFIG
# ----------------------------
DATA_DIR = Path("data")
CHROMA_DIR = Path("chroma")
GEN_MODEL = os.environ.get("GEN_MODEL", "llama3.3")  # modelo gerador
EMB_MODEL = os.environ.get("EMB_MODEL",
                           "nomic-embed-text")  # modelo de embedding
OLLAMA_URL = os.environ.get("OLLAMA_URL", "http://localhost:11434")


# ----------------------------
# 1) CARREGAR E FRAGMENTAR DOCUMENTOS
# ----------------------------
def load_docs():
    docs = []
    if DATA_DIR.exists():
        # PDFs
        pdf_loader = DirectoryLoader(str(DATA_DIR),
                                     glob="**/*.pdf",
                                     loader_cls=PyPDFLoader,
                                     show_progress=True)
        # TXT
        txt_loader = DirectoryLoader(str(DATA_DIR),
                                     glob="**/*.txt",
                                     loader_cls=TextLoader,
                                     show_progress=True)
        for loader in (pdf_loader, txt_loader):
            try:
                docs.extend(loader.load())
            except Exception as e:
                print("Erro ao carregar docs:", e)
    return docs


def split_docs(docs):
    splitter = RecursiveCharacterTextSplitter(
        chunk_size=800,
        chunk_overlap=120,
        separators=["\n\n", "\n", ".", " ", ""])
    return splitter.split_documents(docs)


# ----------------------------
# 2) INDEXAR (EMBEDDINGS + VETORSTORE)
# ----------------------------
def build_or_load_vectorstore(splits):
    embeddings = OllamaEmbeddings(model=EMB_MODEL, base_url=OLLAMA_URL)
    CHROMA_DIR.mkdir(exist_ok=True)
    if splits:
        vs = Chroma.from_documents(documents=splits,
                                   embedding=embeddings,
                                   persist_directory=str(CHROMA_DIR),
                                   collection_name="meu_rag")
        vs.persist()
    else:
        vs = Chroma(embedding_function=embeddings,
                    persist_directory=str(CHROMA_DIR),
                    collection_name="meu_rag")
    return vs


# ----------------------------
# 3) MONTAR O CHAIN DE RAG
# ----------------------------
def make_rag_chain(vectorstore):
    retriever = vectorstore.as_retriever(search_type="mmr",
                                         search_kwargs={"k": 4})

    def format_docs(docs):
        return "\n\n".join(
            f"[{i+1}] {d.page_content}\n(Metadata: {d.metadata})"
            for i, d in enumerate(docs))

    system_pt = (
        "Você é um assistente que responde em português do Brasil com base no CONTEXTO recuperado.\n"
        "Se a resposta não estiver no contexto, diga claramente que não encontrou e sugira onde procurar.\n"
        "Cite trechos relevantes usando o formato [n]. Seja conciso e técnico quando apropriado."
    )

    prompt = ChatPromptTemplate.from_messages([
        ("system", system_pt),
        ("human", "Pergunta: {question}\n\nCONTEXT0:\n{context}"),
    ])

    llm = ChatOllama(model=GEN_MODEL, base_url=OLLAMA_URL, temperature=0.2)

    # pipeline RAG: pergunta -> retrieve -> prompt -> LLM -> texto
    chain = ({
        "context": retriever | format_docs,
        "question": RunnablePassthrough()
    }
             | prompt
             | llm
             | StrOutputParser())
    return chain


# ----------------------------
# 4) CLI SIMPLES
# ----------------------------
def main():
    print("Carregando documentos de", DATA_DIR.resolve())
    docs = load_docs()
    if not docs:
        print(
            "Nenhum documento encontrado; seguindo apenas com base no conhecimento do modelo."
        )
    splits = split_docs(docs) if docs else []
    print(f"{len(splits)} chunks prontos (se 0, só chat).")

    vs = build_or_load_vectorstore(splits)
    chain = make_rag_chain(vs)

    print("\nRAG pronto. Faça perguntas (Ctrl+C para sair).")
    while True:
        q = input("\n> ")
        if not q.strip():
            continue
        ans = chain.invoke(q)
        print("\n" + ans)


if __name__ == "__main__":
    main()
