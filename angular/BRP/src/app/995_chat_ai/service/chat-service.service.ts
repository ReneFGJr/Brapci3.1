import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root',
})
export class ChatServiceService {
  private apiUrl = 'http://143.54.112.91:11434/api/generate'; // Substitua pela URL correta da API do Ollama

  constructor(private http: HttpClient) {}

  sendMessage(message: string): Observable<any> {
    const headers = new HttpHeaders({
      'Content-Type': 'application/json',
      // Adicione outros headers, se necessário, como autenticação
    });

    const body = {
      prompt: message,
      model: "llama3"
      // Adicione outros parâmetros conforme a especificação da API do Ollama
    };

    return this.http.post<any>(this.apiUrl, body, { headers });
  }
}
