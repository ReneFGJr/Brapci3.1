import { Component } from '@angular/core';
import { FormBuilder, FormControl, FormGroup, Validators } from '@angular/forms';
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
    private formBuilder: FormBuilder
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
                  if (!this.data.exist)
                    {
                        this.message = 'Novo Usuário';
                        this.assignup = 'ASSIGN'
                    } else {
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
}
