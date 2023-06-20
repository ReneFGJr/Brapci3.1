import { Router } from '@angular/router';
import { Observable } from 'rxjs';
import { Component, Input, OnInit } from '@angular/core';
import { UIuser } from '../../interface/UIusers';
import { UserService } from '../../service/user.service';
import { FormBuilder } from '@angular/forms';
import { JsonPipe } from '@angular/common';


@Component({
  selector: 'app-user-login',
  templateUrl: './user-login.component.html',
  styleUrls: ['./user-login.component.css']
})


export class UserLoginComponent {

  constructor(
      private fb: FormBuilder,
      private UserService: UserService,
      private Router: Router) { }

  result: any;
  public UIoauth: Array<any> = [];
  public res: Array<any> = [];
  public login: string = '';
  public password: string = '';
  public message: string = '';

  @Input() public loginTitle="User Login";

  public loginSubmit()
    {
    this.UserService.loginSubmitHttp(this.login, this.password).subscribe(
        res => {
            this.result = res;
            let loged = this.UserService.checkLogin(res);
            if (loged)
              {
                  window.location.reload();
                  //this.Router.navigate(['/'])
                  console.log('Loged');
              } else {
                this.message = this.result['message'] + ' ' + this.result['error'];
              }
      },
      error =>
        {
          console.log('ERRO:'+error);
        }
    );
  }
}
