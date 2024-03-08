import { Component } from '@angular/core';
import { NgForm, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { UserService } from '../../../../service/user.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-oauth-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.scss'],
})
export class LoginComponent {
  result: any;

  public data: Array<any> | any;
  constructor(
    private formBuilder: FormBuilder,
    private userService: UserService,
    private router: Router
  ) {}

  public oauthForm: FormGroup = this.formBuilder.group({
    email: ['', Validators.required],
    password: ['', Validators.required],
  });

  public oauthSignUp: FormGroup = this.formBuilder.group({
    signup_name: ['', Validators.required],
    signup_email: ['', Validators.required],
    signup_institution: ['', Validators.required],
  });

  public questionForm: FormGroup = this.formBuilder.group({
    email: ['', Validators.required],
    text: ['', Validators.required],
  });

  public forgotForm: FormGroup = this.formBuilder.group({
    email: ['', Validators.required],
  });
  public Prism = '';
  public email: string = '';
  public message: string = '';

  public image_wait: string = 'assets/img/loading_gear.gif';

  ngOnInit() {
    console.log('Login');
    if (this.userService.loged() == true) {
      this.router.navigate(['/']);
    }
  }

  onSubmit(f: NgForm) {}

  showForgotPassword() {
    let prism = document.querySelector('.rec-prism');
    console.log(prism);
    this.Prism = 'p1';
  }

  showThankYou() {
    this.Prism = 'pWait';
    let email = this.questionForm.value.email;
    let text = this.oauthForm.value.text;
    if (this.oauthForm.valid) {
      this.Prism = 'pWait';
      this.userService.questionHttp(email, text).subscribe(
        (res) => {
          this.result = res;
          this.result['message'] + ' ' + this.result['error'];
          this.Prism = 'pError';
          this.message = 'Dados inválidos';
        },
        (error) => {
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

  signin() {
    if (this.oauthForm.valid) {
      this.Prism = 'pWait';
      this.userService
        .loginSubmitHttp(
          this.oauthForm.value.email,
          this.oauthForm.value.password
        )
        .subscribe(
          (res) => {
            this.result = res;
            let loged = this.userService.checkLogin(res);
            if (loged) {
              this.router.navigate(['/']);
            } else {
              this.message =
                this.result['message'] + ' ' + this.result['error'];
              this.Prism = 'pError';
              this.message = 'Dados inválidos';
            }
          },
          (error) => {
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

  signUP() {
    this.userService
      .signUp(
        this.oauthSignUp.value.signup_name,
        this.oauthSignUp.value.signup_email,
        this.oauthSignUp.value.signup_institution
      )
      .subscribe((res) => {
        console.log(res);
        this.data = res;
        this.message = this.data.message;
        this.Prism = 'pError';
      });
  }

  forgout() {
    if (this.forgotForm.valid) {
      this.Prism = 'pWait';

      this.userService
        .forgotHttp(this.forgotForm.value.email)
        .subscribe((res) => {
          console.log(res);
          this.data = res;
          this.message = this.data.message;
          this.Prism = 'pError';
        });
    } else {
      this.message = '<h1>Erro</h1><p>Dados inválidos</p>';
      this.Prism = 'pError';
    }
  }

  moveBTN(cl: string) {
    this.Prism = cl;
  }
}
