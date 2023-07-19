import { CookieService } from 'ngx-cookie-service';
import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class BrapciService {
  http: any;
  constructor(
    private HttpClient: HttpClient,
  ) { }

  //private url: string = 'https://cip.brapci.inf.br/api/';
  private url: string = 'http://brp/api/';

  public getId(id: string): Observable<Array<any>> {
    let url = `${this.url}brapci/get/v1/`+id;
    console.log(`Buscador: ${url}`);
    var formData: any = new FormData();
    //formData.append('q', id);

    return this.HttpClient.post<Array<any>>(url, formData).pipe(
      res => res,
      error => error
    );
  }

  public search(term: string): Observable<Array<any>> {
    let url = `${this.url}brapci/search/v1`;
    console.log(`Buscador: ${url}`);
    var formData: any = new FormData();
    formData.append('q',term);

    return this.HttpClient.post<Array<any>>(url, formData).pipe(
      res => res,
      error => error
    );
  }
}
