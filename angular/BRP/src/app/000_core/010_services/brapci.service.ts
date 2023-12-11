import { CookieService } from 'ngx-cookie-service';
import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root',
})
export class BrapciService {
  http: any;
  constructor(private HttpClient: HttpClient) {}

  private url: string = 'https://cip.brapci.inf.br/api/';
  //private url: string = 'http://brp/api/';

  public getId(id: number): Observable<Array<any>> {
    let url = `${this.url}brapci/get/v1/` + id;
    console.log(`Buscador: ${url}`);
    var formData: any = new FormData();
    //formData.append('q', id);

    return this.HttpClient.post<Array<any>>(url, formData).pipe(
      (res) => res,
      (error) => error
    );
  }

  public api_post(type: string, dt: Array<any> = []): Observable<Array<any>> {
    let url = `${this.url}` + type;
    console.log(`Buscador: ${url}`);
    var formData: any = new FormData();
    for (const key in dt) {
      formData.append(key, dt[key]);
    }

    return this.HttpClient.post<Array<any>>(url, formData).pipe(
      (res) => res,
      (error) => error
    );
  }

  public sources(type: string): Observable<Array<any>> {
    let url = `${this.url}brapci/source/` + type;
    console.log(`Fontes: ${url}`);
    var formData: any = new FormData();

    return this.HttpClient.post<Array<any>>(url, formData).pipe(
      (res) => res,
      (error) => error
    );
  }

  public generic_post(type: string, dt: Array<any>): Observable<Array<any>> {
    let url = `${this.url}brapci/` + type;
    console.log(`Buscador: ${url}`);
    var formData: any = new FormData();
    for (const key in dt) {
      formData.append(key, dt[key]);
    }
    return this.HttpClient.post<Array<any>>(url, formData).pipe(
      (res) => res,
      (error) => error
    );
  }

  public generic(type: string): Observable<Array<any>> {
    let url = `${this.url}brapci/` + type;
    console.log(`Fontes: ${url}`);
    var formData: any = new FormData();

    return this.HttpClient.post<Array<any>>(url, formData).pipe(
      (res) => res,
      (error) => error
    );
  }

  public basket(list: string): Observable<Array<any>> {
    let url = `${this.url}brapci/basket`;
    console.log(`Fontes: ${url}`);
    var formData: any = new FormData();
    formData.append('row', list);

    return this.HttpClient.post<Array<any>>(url, formData).pipe(
      (res) => res,
      (error) => error
    );
  }

  public source(id: number): Observable<Array<any>> {
    let url = `${this.url}brapci/source/` + id;
    console.log(`Fontes: ${url}`);
    var formData: any = new FormData();

    return this.HttpClient.post<Array<any>>(url, formData).pipe(
      (res) => res,
      (error) => error
    );
  }

  public issue(id: number): Observable<Array<any>> {
    let url = `${this.url}brapci/issue/` + id;
    console.log(`Fontes: ${url}`);
    var formData: any = new FormData();

    return this.HttpClient.post<Array<any>>(url, formData).pipe(
      (res) => res,
      (error) => error
    );
  }

  public getIssue(id: number): Observable<Array<any>> {
    let url = `${this.url}brapci/issue/` + id;
    console.log(`GETISSUE: ${url}`);
    var formData: any = new FormData();

    return this.HttpClient.post<Array<any>>(url, formData).pipe(
      (res) => res,
      (error) => error
    );
  }

  public harvestingIssue(id: number): Observable<Array<any>> {
    let url = `${this.url}brapci/oai/getIssue/` + id;
    console.log(`GETISSUE: ${url}`);
    var formData: any = new FormData();

    return this.HttpClient.post<Array<any>>(url, formData).pipe(
      (res) => res,
      (error) => error
    );
  }

  public search(term: string, dt: Array<any>): Observable<Array<any>> {
    let url = `${this.url}brapci/search/v1`;
    console.log(`Buscador: ${url}`);
    var formData: any = new FormData();
    formData.append('q', term);
    formData.append('offset', '1000');

    for (const key in dt) {
      formData.append(key, dt[key]);
    }

    return this.HttpClient.post<Array<any>>(url, formData).pipe(
      (res) => res,
      (error) => error
    );
  }

  public donwload(nd: string) {
    console.log(nd);
  }
}
