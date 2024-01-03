import { CookieService } from 'ngx-cookie-service';
import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { LocalStorageService } from '../service/local-storage.service';

@Injectable({
  providedIn: 'root'
})
export class UserService {
  http: any;
  constructor(
    private HttpClient: HttpClient,
    private CookieService: CookieService,
    private LocalStorageService: LocalStorageService,
  ) { }

  public user: Array<any> = [];
  public logged: boolean = false;

  /************************************************ LogOut */
  public logout() {
    this.LocalStorageService.remove('user');
    this.CookieService.delete('token');
    //this.UserNavBarComponent.loged = false;
  }

  /************************************************ GETUSER */
  public getUser() {
    this.user = this.LocalStorageService.get('user');
    if (this.user == null) {
      return [];
    }
    else {
      this.user = this.user[0];
      return this.user;
    }
  }

  /************************************************ CHECKLOGIN */
  public checkLogin(res: any): boolean {
    if (res['status'] == '200') {
      /*********************** Cookie */
      this.CookieService.set('token', res['token']);

      /*********************** Push */
      this.user = [];
      this.user.push({
        id: res['id'], displayName: res['displayName'], email: res['email'],
        givenName: res['givenName'], sn: res['sn'],
        token: res['token'], persistentId: res['persistent-id']
      });

      /*********************** LocalStorage */
      this.LocalStorageService.set('user', this.user);

      return true;
    } else {
      return false;
    }
  }

  /************************************************ LOGED */
  public loged(): boolean {
    this.user = this.LocalStorageService.get('user');
    if (this.user == null) {
        return false;
    }
    else {
      this.user = this.user[0];
        return true;
    }
  }

  private url: string = 'https://cip.brapci.inf.br/api/';
  //private url: string = 'http://brp/api/';

  /************************************************ API CONSULTA */
  public signUp(name: string, email:string, institution:string): Observable<Array<any>> {
    let url = `${this.url}socials/signup`;
    console.log(url)

    var formData: any = new FormData();
    formData.append('signup_name', name);
    formData.append('signup_email', email);
    formData.append('signup_institution', institution);

    return this.HttpClient.post<Array<any>>(url, formData).pipe(
      (res) => res,
      (error) => error
    );
  }
  public loginSubmitHttp(login: string, pass: string): Observable<Array<any>> {
    let url = `${this.url}socials/signin`;

    var formData: any = new FormData();
    formData.append('user', login);
    formData.append('pwd', pass);

    return this.HttpClient.post<Array<any>>(url, formData).pipe(
      res => res,
      error => error
    );
  }
}
