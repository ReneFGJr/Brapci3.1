import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root',
})
export class OllamaService {
  private apiUrl = 'https://cip.brapci.inf.br/api/ai/chat'; // Substitua pela URL real da API do Ollama
  //private apiUrl = 'http://143.54.112.91:11434/api/generate'; // Substitua pela URL real da API do Ollama

  private apiKey = 'YOUR_OLLAMA_API_KEY'; // Substitua pela sua chave de API

  constructor(private http: HttpClient) {}

  sendMessage(message: string): Observable<any> {
    const headers = new HttpHeaders({
      'Content-Type': 'application/json',
      //Authorization: `Bearer ${this.apiKey}`,
    });

    const body = {
      message: message,
      // Adicione outros campos necessários conforme a documentação da API do Ollama
    };

    return this.http.post<any>(this.apiUrl, body, { headers });
  }
}
