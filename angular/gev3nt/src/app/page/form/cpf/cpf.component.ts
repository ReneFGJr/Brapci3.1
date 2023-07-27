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
    });       
  }
  public message: string = '';
  public message2: string = '';
  public valid: string = '';
  public assignup: string = '';
  public assignIn: Array<any> | any;
  public ncpf: string = '';


  cpf = new FormControl([]);
  data:Array<any> | any

  onSubmit2()
    {}

  onSubmit()
    {
      if (this.meuCPF.valid)
        {
          this.message = ''
          console.log(this.meuCPF)
          console.log(this.meuCPF.value.cpf)
          this.brapciService.getCPF(this.meuCPF.value.cpf).subscribe(
            res=>{
              this.data = res;
              if (!this.data.valid)
                {
                    this.message = 'CPF Inválido ' + this.data.cpf;
                } else {
                  console.log(this.data.exist)
                  if (!this.data.exist)
                    {
                        this.message = 'Novo Usuário';
                        this.assignup = 'ASSIGN'
                        this.ncpf = this.data.cpf
                    } else {
                      this.ncpf = this.data.cpf
                      this.assignIn = this.data.data
                      console.log(this.assignIn.data)
                    }
                }
              
              console.log(res);
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
      }
}
