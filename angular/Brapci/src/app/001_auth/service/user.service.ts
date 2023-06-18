import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { UIuser } from '../interface/UIusers';
import { Router } from '@angular/router';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class UserService {
  constructor(
    private HttpClient: HttpClient,
    private Router: Router) { }

  private user: Array<UIuser> = [];

  public userProfile() {
    return this.user;
  }

  private url: string = 'https://cip.brapci.inf.br/api/';
  //private url: string = 'http://validate.jsontest.com/';

  public signIn(login:string, pass:string): Observable<Array<UIuser>>
    {
    let url = `${this.url}socials/signin`;

/*
    let data = {
      "email": login,
      "password": pass
    }
*/

    let data = 'user=renefgj@gmail.com&pwd=545448';
    data = JSON.stringify(data)
    console.log(data);

    let headers = new HttpHeaders();
    headers.append('Content-Type', 'application/json');

    return this.HttpClient.post<Array<UIuser>>(url, data).pipe(
      res=>res,
      error=>error
    );

    }
}
