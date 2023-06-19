import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders, HttpParams } from '@angular/common/http';
import { UIuser } from '../interface/UIusers';
import { UIoauth } from '../interface/UIoauth';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class UserService {
  http: any;
  constructor(
    private HttpClient: HttpClient) { }

  private user: Array<UIuser> = [];

  public userProfile() {
    return this.user;
  }

  private url: string = 'https://cip.brapci.inf.br/api/';
  //private url: string = 'http://brp/api/';

  public loginSubmitHttp(login: string, pass: string): Observable<Array<any>> {
    let url = `${this.url}socials/signin`;

    var formData: any = new FormData();
    formData.append('user', login);
    formData.append('pwd', pass);

    return this.HttpClient.post<Array<any>>(url, formData).pipe(
      res => res,
      error => error
    );
  }

  public signIn2(login: string, pass: string): Observable<Array<UIoauth>> {
    let url = `${this.url}socials/signin`;

    var formData: any = new FormData();
    formData.append('user', login);
    formData.append('pwd', pass);

    return this.HttpClient.post<Array<UIoauth>>(url, formData).pipe(
      res => res,
      error => error
    );
  }
}
