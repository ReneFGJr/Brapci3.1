import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root',
})
export class ChatService {
  private apiUrl = 'http://143.54.112.91:11434/api/generate'; // Endpoint do Ollama

  constructor(private http: HttpClient) {}

  sendMessage(message: string): Observable<any> {
    const headers = new HttpHeaders({
      'Content-Type': 'application/json',
    });

    const body = {
      prompt: message,
    };

    return this.http.post<any>(`${this.apiUrl}/api/chat`, body, { headers });
  }
}
