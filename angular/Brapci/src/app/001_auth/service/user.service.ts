import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
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

  private url: string = 'http://brp/api/socials/';

  public signIn(login:string, pass:string): Observable<Array<UIuser>>
    {
    let url = `${this.url}signin`;
    console.log(url);
    console.log(login);
    console.log(pass);
    //return this.HttpClient.post<Array<UIuser>>(url,{login:login,pwd:pass}).pipe(
    return this.HttpClient.get<Array<UIuser>>(url).pipe(
      res=>res,
      error=>error
    );
    }


}
