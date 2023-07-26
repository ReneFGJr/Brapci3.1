import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs/internal/Observable';


@Injectable({
  providedIn: 'root'
})
export class BrapciService {

  http: any;
  constructor(
    private HttpClient: HttpClient,
  ) { }
  
  private url: string = 'https://cip.brapci.inf.br/api/';
  //private url: string = 'http://brp/api/';  


  public getCPF(cpf: string): Observable<Array<any>> {
    let url = `${this.url}authority/cpf/${cpf}`;
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

}
