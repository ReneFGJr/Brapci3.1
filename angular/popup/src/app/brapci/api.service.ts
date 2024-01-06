import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root',
})
export class ApiService {
  http: any;

  private url: string = 'https://cip.brapci.inf.br/api/';

  constructor(private HttpClient: HttpClient) {}

  public api(id: number,term:string,prop:string): Observable<Array<any>> {
    let url = `${this.url}rdf/search/`;
    console.log(`Fontes: ${url}`);
    console.log(`Params: ${term},${id},${prop}`);
    var formData: any = new FormData();
    formData.append('q', term);
    formData.append('concept', id);
    formData.append('propriey', prop);

    return this.HttpClient.post<Array<any>>(url, formData).pipe(
      (res) => res,
      (error) => error
    );
  }
}
