import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { firstValueFrom, Observable } from 'rxjs';
import { map, catchError } from 'rxjs/operators';

@Injectable({
  providedIn: 'root',
})
export class ChatServiceService {
  //private apiUrl = 'https://cip.brapci.inf.br/api/ai/chat';
  private apiUrl = 'http://brp/api/ai/chat';

  constructor(private http: HttpClient) {}

  async sendMessageSync(message: string): Promise<string> {
    const headers = new HttpHeaders({
      'Content-Type': 'application/json',
    });

    const body = {
      model: 'llama3',
      prompt: message,
    };

    console.log('API', this.apiUrl);
    console.log('BODY', body);

    try {
      const response = await firstValueFrom(
        this.http
          .post(this.apiUrl, body, { headers, responseType: 'text' })
          .pipe(
            map((response) => {
              const parsedResponses = response
                .split('\n')
                .map((resp) => JSON.parse(resp));
              const fullResponse = parsedResponses
                .map((resp) => resp.response)
                .join('');
              return fullResponse;
            })
          )
      );
      return response;
    } catch (error) {
      console.error('Erro ao enviar mensagem:', error);
      throw error;
    }
  }

  sendMessage(message: string): Observable<any> {
    const headers = new HttpHeaders({
      'Content-Type': 'application/json',
    });

    const body = {
      model: 'llama3', // Ajuste conforme necessário
      prompt: message,
    };

    return this.http
      .post(this.apiUrl, body, { headers, responseType: 'text' })
      .pipe(
        map((response) => {
          try {
            return JSON.parse(response); // Tenta fazer o parse da resposta como JSON
          } catch (e) {
            console.error('Falha ao fazer parse da resposta:', e);
            return response; // Retorna a resposta bruta caso não seja JSON
          }
        }),
        catchError((error) => {
          console.error('Erro ao enviar mensagem:', error);
          throw error; // Repassa o erro para ser tratado onde a função é chamada
        })
      );
  }
}
