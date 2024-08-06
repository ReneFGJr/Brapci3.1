// auth.service.ts
import { Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { environment } from '../../../environments/environment';

@Injectable({
  providedIn: 'root',
})
export class AuthService {
  private clientId = environment.clientId;
  /* private redirectUri = 'https://brapci.inf.br/#/callback/orcid/'; */
  private redirectUri = environment.redirectUri
  private tokenUrl = environment.tokenUrl
  private authorizeUrl = environment.authorizeUrl

  constructor(private http: HttpClient) {}

  login() {
    window.location.href = `${this.authorizeUrl}?client_id=${this.clientId}&response_type=code&scope=/authenticate&redirect_uri=${this.redirectUri}`;
  }

  getAccessToken(code: string) {
    const body = new HttpParams()
      .set('client_id', this.clientId)
      .set('client_secret', environment.client_secret)
      .set('grant_type', 'authorization_code')
      .set('code', code)
      .set('redirect_uri', this.redirectUri);

    return this.http.post(this.tokenUrl, body);
  }
}
