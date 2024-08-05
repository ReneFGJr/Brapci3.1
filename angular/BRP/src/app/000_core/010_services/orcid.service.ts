// auth.service.ts
import { Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { environment } from '../../../environments/environment';

@Injectable({
  providedIn: 'root',
})
export class AuthService {
  private clientId = 'APP-5SXE6F14K56K8RQN';
  /* private redirectUri = 'https://brapci.inf.br/#/callback/orcid/'; */
  private redirectUri = 'https://cip.brapci.inf.br/api/callback/orcid';
  private tokenUrl = 'https://orcid.org/oauth/token';
  private authorizeUrl = 'https://orcid.org/oauth/authorize';

  constructor(private http: HttpClient) {}

  login() {
    window.location.href = `${this.authorizeUrl}?client_id=${this.clientId}&response_type=code&scope=/authenticate&redirect_uri=${this.redirectUri}`;
  }

  getAccessToken(code: string) {
    const body = new HttpParams()
      .set('client_id', this.clientId)
      .set('client_secret', '93a45b75-b66c-41bc-a13a-54aaf3746df4')
      .set('grant_type', 'authorization_code')
      .set('code', code)
      .set('redirect_uri', this.redirectUri);

    return this.http.post(this.tokenUrl, body);
  }
}
