import { CookieService } from 'ngx-cookie-service';
import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { LocalStorageService } from './local-storage.service';
import { UserService } from 'src/app/001_auth/service/user.service';

@Injectable({
  providedIn: 'root',
})
export class BrapciService {
  http: any;
  apikey:string =  ''
  url_post: string = '';
  public user: Array<any> | any;

  constructor(
    private HttpClient: HttpClient,
    private cookieService: CookieService,
    private userService: UserService
  ) {}

  public url: string = 'https://cip.brapci.inf.br/api/';
  public url_development: string = 'http://brp/api/';

  public getId(id: number): Observable<Array<any>> {
    let url = 'brapci/get/v1/' + id;

    let session = this.cookieService.get('section');

    let dt: Array<any> | any = [{ session: session }];

    let rsp =  this.api_post(url, dt);
    return rsp
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
    let dt: Array<any> | any = [{ q: term, concept: id, propriey: prop }];
    let rsp = this.api_post(url, dt);
    return rsp
  }

  public api_post(
    type: string,
    dt: Array<any> = [],
    development: boolean = false
  ): Observable<Array<any>> {
    var formData: any = new FormData();
    let section = this.cookieService.get('section');
    this.user = this.userService.getUser();

    if (!this.user)
      {
        this.apikey = '';
      }
      else {
        this.apikey = this.user.token;
      }

    if (development) {
      this.url_post = `${this.url_development}` + type;
    } else {
      this.url_post = `${this.url}` + type;
    }

    formData.append('section', section);
    formData.append('user', this.apikey);

    for (const key in dt) {
      formData.append(key, dt[key]);
    }
     console.log('URL', this.url_post);
    return this.HttpClient.post<Array<any>>(this.url_post, formData).pipe(
      (res) => res,
      (error) => error,
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

    this.user = this.userService.getUser();

    if (!this.user) {
      this.apikey = '';
    } else {
      this.apikey = this.user.token;
    }

    formData.append('q', term);
    formData.append('offset', '1000');
    formData.append('user', this.apikey);

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
