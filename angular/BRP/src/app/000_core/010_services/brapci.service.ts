import { CookieService } from 'ngx-cookie-service';
import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root',
})
export class BrapciService {
  http: any;
  constructor(
    private HttpClient: HttpClient,
    private cookieService: CookieService,
    ) {}

  public url: string = 'https://cip.brapci.inf.br/api/';
  //public url: string = 'http://brp/api/';

  public getId(id: number): Observable<Array<any>> {
    let url = 'brapci/get/v1/' + id;
    let dt: Array<any> | any = [];

    return this.api_post(url,dt)
  }

  public RDFapi(
    act: string,
    ID: string,
    prop: string,
    term: string
  ): Observable<Array<any>> {
    let url = `${this.url}` + act;
    console.log(url);
    var formData: any = new FormData();
    formData.append('q', term);
    formData.append('concept', ID);
    formData.append('propriey', prop);

    return this.HttpClient.post<Array<any>>(url, formData).pipe(
      (res) => res,
      (error) => error
    );
  }

  public api(id: number, term: string, prop: string): Observable<Array<any>> {
    let url = 'rdf/search/';
    let dt: Array<any> | any = [{ q: term, concept: id, propriey: prop}];
    return this.api_post(url, dt);
  }

  public api_post(type: string, dt: Array<any> = []): Observable<Array<any>> {
    let url = `${this.url}` + type;
    console.log(`Buscador: ${url}`);
    var formData: any = new FormData();
    let apikey = this.cookieService.get('section');
    formData.append('user', apikey);

    for (const key in dt) {
      formData.append(key, dt[key]);
    }

    return this.HttpClient.post<Array<any>>(url, formData).pipe(
      (res) => res,
      (error) => error
    );
  }

  public sources(type: string): Observable<Array<any>> {
    let url = `brapci/source/` + type;
    let dt: Array<any> | any = [];
    return this.api_post(url, dt);
  }

  public basket(list: string): Observable<Array<any>> {
    let url = `${this.url}brapci/basket`;
    var formData: any = new FormData();
    formData.append('row', list);
    let apikey = this.cookieService.get('section');
    formData.append('user', apikey);

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
    let apikey = this.cookieService.get('section');
    formData.append('user', apikey);

    return this.HttpClient.post<Array<any>>(url, formData).pipe(
      (res) => res,
      (error) => error
    );
  }

  public getIssue(id: number): Observable<Array<any>> {
    let url = `brapci/issue/` + id;
    let dt: Array<any> | any = [];
    return this.api_post(url, dt);
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

  public searchAdv(dt: Array<any>) {
    console.log(dt);
    let url = `${this.url}brapci/search/a1`;
    console.log(`Buscador: ${url}`);
    var formData: any = new FormData();
    formData.append('offset', '1000');

    formData.append('data', dt);

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
