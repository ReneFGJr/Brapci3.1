import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { UserService } from 'src/app/001_auth/service/user.service';
import { UIuser } from 'src/app/001_auth/interface/UIusers';

@Injectable({
  providedIn: 'root'
})
export class VitrineLivrosService {

  constructor(private HttpClient: HttpClient,
    private userService: UserService
    ) { }

  //private url: string = 'https://cip.brapci.inf.br/api/';
  private url: string = 'http://brp/api/';
  public library: number = 1;
  usr = [];
  token = 'Invalid';

  public httpOption = new HttpHeaders(
    {
      'Content-Type': 'application/json',
      'Authenticatio': 'KEY'
    }
  );

  public insertISBN(isbn: string): Observable<Array<any>> {
    let url = `${this.url}find/isbn/${isbn}/add`;

    this.usr = <any>this.userService.getUser();
    console.log(url);
    console.log(this.userService.user);
    console.log('+++++'+this.token);

    var formData: any = new FormData();
    formData.append('library', this.library);
    //formData.append('apikey', this.usr.token);

    return this.HttpClient.post<Array<any>>(url, formData).pipe(
      res => res,
      error => error
    );
  }

  public validISBN(isbn: string): Observable<Array<any>> {
    let url = `${this.url}isbn/${isbn}`;
    console.log(url);

    return this.HttpClient.get<Array<any>>(url).pipe(
      res => res,
      error => error
    );
  }

  public listPreparo(): Observable<Array<any>> {
    let url = `${this.url}find/status/0`;
    console.log(url);

    var formData: any = new FormData();
    formData.append('library', this.library);

    return this.HttpClient.post<Array<any>>(url, formData).pipe(
      res => res,
      error => error
    );
  }

  public getBook(id: string): Observable<Array<any>> {
    let url = `${this.url}find/getID/${id}`;
    console.log(url);

    return this.HttpClient.get<Array<any>>(url).pipe(
      res => res,
      error => error
    );
  }

  /************************************************ API CONSULTA */
  public listBooks(): Observable<Array<any>> {
    let url = `${this.url}find/vitrine/0/50`;

    var formData: any = new FormData();
    formData.append('library', this.library);

    return this.HttpClient.post<Array<any>>(url, formData).pipe(
      res => res,
      error => error
    );
  }
}
