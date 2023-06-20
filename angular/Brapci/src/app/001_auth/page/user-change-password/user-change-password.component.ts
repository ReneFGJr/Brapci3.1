import { Component } from '@angular/core';

@Component({
  selector: 'app-user-change-password',
  templateUrl: './user-change-password.component.html',
  styleUrls: ['./user-change-password.component.css']
})
export class UserChangePasswordComponent {
    public password:string = '';
    public password_01: string = '';
    public password_02: string = '';
    public message:string = '';
    public loginTitle = 'Change Password';

  loginSubmit()
    {

    }
}
