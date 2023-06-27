import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class VitrineLivrosService {

  constructor(private HttpClient: HttpClient) { }

  //private url: string = 'https://cip.brapci.inf.br/api/';
  private url: string = 'http://brp/api/';
  public library:number = 1;

  public httpOption = new HttpHeaders(
    {
      'Content-Type': 'application/json',
      'Authenticatio': 'KEY'
    }
  );

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
    formData.append('library',this.library);

      return this.HttpClient.post<Array<any>>(url, formData).pipe(
      res => res,
      error => error
    );
  }
}
