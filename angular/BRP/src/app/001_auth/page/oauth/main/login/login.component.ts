import { Component } from '@angular/core';
import { NgForm, FormBuilder, FormGroup, Validators } from '@angular/forms';

@Component({
  selector: 'app-oauth-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.scss']
})
export class LoginComponent {

  public oauthForm: FormGroup = this.formBuilder.group(
    {
      email: ['',Validators.required],
      password: ['', Validators.required]
    }
  );

  public forgotForm: FormGroup = this.formBuilder.group(
    {
      email: ['', Validators.required]
    }
  );
  public Prism = "";
  public email:string = "";
  public message: string = "";

  constructor(
    private formBuilder: FormBuilder
  ) {}

  onSubmit(f: NgForm) {
  }

  showForgotPassword()
    {
      alert("Hello");
      let prism = document.querySelector(".rec-prism");
      console.log(prism);
      this.Prism = 'p1';
    }

  signin()
    {
    if (this.oauthForm.valid)
        {
          this.Prism = 'pWait';
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

  moveBTN(cl:string) {
    this.Prism = cl;
  }
}
