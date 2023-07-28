import { Component } from '@angular/core';
import { FormBuilder, FormControl, FormGroup, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { BrapciService } from 'src/app/service/brapci.service';

@Component({
  selector: 'app-cpf',
  templateUrl: './cpf.component.html',
  styleUrls: ['./cpf.component.scss']
})
export class CpfComponent {
  meuCPF: FormGroup;
  meuCadastro: FormGroup;
  
  constructor(
    private brapciService:BrapciService,
    private formBuilder: FormBuilder,
    private router: Router
  ) {
    this.meuCPF = this.formBuilder.group({
      cpf: ['', Validators.required],
    });

    this.meuCadastro = this.formBuilder.group({
      name: ['', Validators.required],
      email: ['', Validators.required],
      email_alt: [''],
    });       
  }
  public message: string = '';
  public message2: string = '';
  public valid: string = '';
  public assignup: string = '';
  public assignIn: Array<any> | any;
  public ncpf: string = '';

  public events: Array<any> | any;


  cpf = new FormControl([]);
  data:Array<any> | any

  change(id:string)
    {
      alert(id)
    }

  onSubmit2()
    {
      if (this.meuCadastro.valid)
        {
          this.message = ''
          this.brapciService.register(this.meuCPF.value.cpf,this.meuCadastro.value.name, this.meuCadastro.value.email, this.meuCadastro.value.email_alt).subscribe(
            res=>{
              this.data = res;
              this.meuCadastro.value.name = '';
              this.meuCadastro.value.email = '';
              this.meuCadastro.value.email_alt = '';
              this.assignup = '';
              this.onSubmit();
            }
          )
        } else {
          this.message = 'CPF Inválido'
        }
    }

  onSubmit()
    {
      if (this.meuCPF.valid)
        {
          this.message = ''
          this.brapciService.getCPF(this.meuCPF.value.cpf).subscribe(
            res=>{
              this.data = res;
              /***************** Verifica CPF */
              if (!this.data.valid)
                {
                    /***************** CPF Inválido */
                    this.message = 'CPF Inválido ' + this.data.cpf;
                } else {
                  /***************** CPF Válido */
                  if (!this.data.exist)
                    {
                        /***************** CPF Novo */
                        this.message = 'Novo Usuário';
                        this.assignup = 'ASSIGN'
                        this.ncpf = this.data.cpf
                    } else {
                      /***************** CPF Já cadastrado */
                      this.ncpf = this.data.cpf
                      this.assignIn = this.data.data
                      this.brapciService.events().subscribe(
                        res=>{
                            console.log(res)
                            this.events = res
                        }
                      )
                      
                      console.log(this.assignIn.data)
                    }
                }
            }
          )
        } else {
          this.message = 'CPF Inválido'
        }
      /*
      this.brapciService.getCPF(ncpf).subscribe(
        res=>{
          console.log(res);
        }
      )
      */

    }

    return()
      {
        this.ncpf = ''
        this.assignIn = ''
        this.events = []
      }
}
