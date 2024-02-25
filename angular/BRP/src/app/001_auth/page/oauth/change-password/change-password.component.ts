import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormControl, FormGroup } from '@angular/forms';
import { ActivatedRoute } from '@angular/router';
import { SocialChangePassword } from 'src/app/000_class/social-change-password';
import { BrapciService } from 'src/app/000_core/010_services/brapci.service';

@Component({
  selector: 'app-change-password',
  templateUrl: './change-password.component.html',
})
export class ChangePasswordComponent {
  formCliente: FormGroup<any> | any;
  apikey:string = ''
  parms:Array<any>|any

  ngOnInit() {
    this.createForm(new SocialChangePassword());

    this.parms = this.route.params.subscribe((params) => {
      this.apikey = params['id']; // (+) converts string 'id' to a number
      this.formCliente.controls['apikey'].setValue(this.apikey);

      let url = 'socials/validApiRecover'
      let dt: Array<any> | any = {apikey: this.apikey,action:'Recovery' };

      this.brapciService.api_post(url,dt).subscribe(
        (res) => {
          console.log(res)
        });


    });
  }

  constructor(
    private formBuilder: FormBuilder,
    private brapciService: BrapciService,
    private route: ActivatedRoute
  ) {}

  createForm(cliente: SocialChangePassword) {
    this.formCliente = this.formBuilder.group({
      pass1: [cliente.pass1],
      pass2: [cliente.pass2],
      apikey: [cliente.apikey],
    });
  }

  onSubmit() {
    // aqui você pode implementar a logica para fazer seu formulário salvar
    console.log(this.formCliente.value);

    // chamando a função createForm para limpar os campos na tela
    this.createForm(new SocialChangePassword());
  }
}
