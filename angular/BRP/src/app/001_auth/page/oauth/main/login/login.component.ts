import { Component } from '@angular/core';
import { NgForm, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { UserService } from '../../../../service/user.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-oauth-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.scss']
})
export class LoginComponent {

  result: any;
  constructor(
    private formBuilder: FormBuilder,
    private userService: UserService,
    private router: Router
  ) { }

  public oauthForm: FormGroup = this.formBuilder.group(
    {
      email: ['', Validators.required],
      password: ['', Validators.required]
    }
  );

  public forgotForm: FormGroup = this.formBuilder.group(
    {
      email: ['', Validators.required]
    }
  );
  public Prism = "";
  public email: string = "";
  public message: string = "";

  ngOnInit()
    {
        console.log('Login');
        if (this.userService.loged() == true)
          {
            this.router.navigate(['/']);
          }
    }

  onSubmit(f: NgForm) {
  }

  showForgotPassword() {
    alert("Hello");
    let prism = document.querySelector(".rec-prism");
    console.log(prism);
    this.Prism = 'p1';
  }

  signin() {
    if (this.oauthForm.valid) {
      this.Prism = 'pWait';
      this.userService.loginSubmitHttp(this.oauthForm.value.email, this.oauthForm.value.password).subscribe(
        res => {
          this.result = res;
          let loged = this.userService.checkLogin(res);
          if (loged) {
            this.router.navigate(['/']);
          } else {
            this.message = this.result['message'] + ' ' + this.result['error'];
            this.Prism = 'pError';
            this.message = 'Dados inválidos';
          }
        },
        error => {
          console.log('ERRO:' + error);
          this.message = 'Dados inválidos';
          this.Prism = 'pError';
        }
      );

    } else {
      this.message = 'Dados inválidos';
      this.Prism = 'pError';
    }
  }

  forgout() {
    if (this.forgotForm.valid) {
      this.Prism = 'pWait';
    } else {
      this.message = 'Dados inválidos';
      this.Prism = 'pError';
    }
  }

  moveBTN(cl: string) {
    this.Prism = cl;
  }
}
