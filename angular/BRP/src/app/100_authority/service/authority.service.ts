import { CookieService } from 'ngx-cookie-service';
import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class AuthorityService {
  http: any;
  constructor(
    private HttpClient: HttpClient,
  ) { }

  //private url: string = 'https://cip.brapci.inf.br/api/';
  private url: string = 'http://brp/api/';

  public getId(id: number): Observable<Array<any>> {
    let url = `${this.url}authority/getid/${id}`;
    console.log(`Authority GetID: ${url}`);
    var formData: any = new FormData();
    /*
    formData.append('user', login);
    formData.append('pwd', pass);
    */
    return this.HttpClient.post<Array<any>>(url, formData).pipe(
      res => res,
      error => error
    );
  }

  /************************************************ API CONSULTA */
  public searchList(term: string, type: string): Observable<Array<any>> {
    let url = `${this.url}authority/search/${term}/${type}`;

    console.log("Authority Search: "+url);

    var formData: any = new FormData();

    /*
    formData.append('user', login);
    formData.append('pwd', pass);
    */

    return this.HttpClient.post<Array<any>>(url, formData).pipe(
      res => res,
      error => error
    );
  }
}
