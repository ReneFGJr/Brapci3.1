import { Observable } from 'rxjs';
import { Component, Input, OnInit } from '@angular/core';
import { UIuser } from '../../interface/UIusers';
import { UserService } from '../../service/user.service';
import { FormBuilder } from '@angular/forms';


@Component({
  selector: 'app-user-login',
  templateUrl: './user-login.component.html',
  styleUrls: ['./user-login.component.css']
})


export class UserLoginComponent {

  constructor(private fb: FormBuilder, private UserService: UserService) { }

  [x: string]: any;
  public message = "";
  public UIuser: Array<UIuser> = [];
  public login: string = '';
  public password: string = '';
  @Input() public loginTitle="User Login";

  public loginSubmit()
    {
    console.log('User '+this.login);
    console.log('Password '+this.password);
      return this.UserService.signIn(this.login,this.password).subscribe(
        res=>res,
        error=>console.log(error)
      )
    }
}
