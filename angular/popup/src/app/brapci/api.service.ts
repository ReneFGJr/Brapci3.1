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

  public api(id: number,term:string): Observable<Array<any>> {
    let url = `${this.url}rdf/search/` + id;
    console.log(`Fontes: ${url}`);
    var formData: any = new FormData();
    formData.append('term', term);
    formData.append('concept', id);

    return this.HttpClient.post<Array<any>>(url, formData).pipe(
      (res) => res,
      (error) => error
    );
  }
}
