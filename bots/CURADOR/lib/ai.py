import requests
from config import LLM


def perguntar(messages):

    r = requests.post(
        f"{LLM['url']}/api/chat",
        json={
            "model": LLM["model"],
            "messages": messages,
            "stream": False
        },
        timeout=300
    )

    r.raise_for_status()

    return r.json()["message"]["content"]