import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders, HttpParams } from '@angular/common/http';
import { UIuser } from '../interface/UIusers';
import { Router } from '@angular/router';
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

  //private url: string = 'https://cip.brapci.inf.br/api/';
  //private url: string = 'http://validate.jsontest.com/';
  private url: string = 'http://brp/api/';

  public signIn(login:string, pass:string): Observable<Array<UIuser>>
    {
    let url = `${this.url}socials/signin`;

    var formData: any = new FormData();
    formData.append('user', login);
    formData.append('pwd', pass);

    return this.HttpClient.post<Array<UIuser>>(url, formData).pipe(
      res=>res,
      error=>error
    );

    }
}
