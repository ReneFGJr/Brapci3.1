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

  public register(cpf: string, name: string, email:string, email_alt:string): Observable<Array<any>> {  
      let url = `${this.url}authority/put/${name}`;
      var formData: any = new FormData();
      
      formData.append('cpf', cpf);
      formData.append('name', name);
      formData.append('email', email);
      formData.append('email_alt', email_alt);
      return this.HttpClient.post<Array<any>>(url, formData).pipe(
        res => res,
        error => error
      );
        
  };

  public events(): Observable<Array<any>> {  
    let url = `${this.url}gev3nt/events`;
    var formData: any = new FormData();
    //formData.append('cpf', cpf);
    
    return this.HttpClient.post<Array<any>>(url, formData).pipe(
      res => res,
      error => error
    );      
};

public getEvent(id:string): Observable<Array<any>> {  
  let url = `${this.url}gev3nt/get/`+id;
  var formData: any = new FormData();
  //formData.append('cpf', cpf);
  
  return this.HttpClient.post<Array<any>>(url, formData).pipe(
    res => res,
    error => error
  );      
};

public getSections(id:string,cpf:string): Observable<Array<any>> {  
  let url = `${this.url}gev3nt/sections/`+id+'/'+cpf;
  var formData: any = new FormData();
  formData.append('cpf', cpf);
  
  return this.HttpClient.post<Array<any>>(url, formData).pipe(
    res => res,
    error => error
  );      
};

public registerEV(id:string,cpf:string,sta:string,ev:string): Observable<Array<any>> {  
  let url = `${this.url}gev3nt/event_register`;
  var formData: any = new FormData();
  formData.append('id', id);
  formData.append('cpf', cpf);
  formData.append('sta', sta);
  formData.append('ev', sta);
  
  return this.HttpClient.post<Array<any>>(url, formData).pipe(
    res => res,
    error => error
  );      
};


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
