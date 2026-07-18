from dotenv import load_dotenv
import os

load_dotenv()

APP_NAME = os.getenv("APP_NAME", "CURADOR")
APP_VERSION = os.getenv("APP_VERSION", "0.1")

DEBUG = os.getenv("DEBUG", "False").lower() == "true"

DB = {
    "host": os.getenv("DB_HOST"),
    "port": int(os.getenv("DB_PORT", 3306)),
    "database": os.getenv("DB_DATABASE"),
    "user": os.getenv("DB_USERNAME"),
    "password": os.getenv("DB_PASSWORD"),
}

LLM = {
    "provider": os.getenv("LLM_PROVIDER", "ollama"),
    "url": os.getenv("OLLAMA_URL"),
    "model": os.getenv("OLLAMA_MODEL"),
    "openai_key": os.getenv("OPENAI_API_KEY"),
    "openai_model": os.getenv("OPENAI_MODEL"),
}

LOG_PATH = os.getenv("LOG_PATH")
TEMP_PATH = os.getenv("TEMP_PATH")
DATA_PATH = os.getenv("DATA_PATH")

MAX_THREADS = int(os.getenv("MAX_THREADS", 4))
TIMEOUT = int(os.getenv("TIMEOUT", 300))