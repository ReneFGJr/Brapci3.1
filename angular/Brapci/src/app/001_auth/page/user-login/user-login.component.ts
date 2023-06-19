import { Observable } from 'rxjs';
import { Component, Input, OnInit } from '@angular/core';
import { UIuser } from '../../interface/UIusers';
import { UIoauth } from '../../interface/UIoauth';
import { UserService } from '../../service/user.service';
import { FormBuilder } from '@angular/forms';
import { JsonPipe } from '@angular/common';


@Component({
  selector: 'app-user-login',
  templateUrl: './user-login.component.html',
  styleUrls: ['./user-login.component.css']
})


export class UserLoginComponent {

  constructor(private fb: FormBuilder, private UserService: UserService) { }

  [x: string]: any;
  result: any;
  public UIuser: Array<UIuser> = [];
  public UIoauth: Array<any> = [];
  public res: Array<any> = [];
  public login: string = '';
  public password: string = '';
  public message: string = '';

  public token:string="";
  public id:number=0;
  public erro: string = '000';

  @Input() public loginTitle="User Login";

  public checkLogin()
    {
      console.log(this.result);
        if (this.result['status'] == '200')
        {

        } else {
        this.message = this.result['message'] + ' ' + this.result['error'];
        }
    }

  public loginSubmit()
    {
    this.UserService.loginSubmitHttp(this.login, this.password).subscribe(
        res => {
            this.result = res;
            this.checkLogin();
      },
      error =>
        {
          console.log(error);
        }
    );
  }

  public loginSubmit2() {
    console.log(this.UIoauth);
    this.UserService.loginSubmitHttp(this.login, this.password).subscribe(
      res => {
        this.UIoauth = res;
        console.log(res);
      },
      error => error
    );
    console.log(this.UIoauth);
  }
}
