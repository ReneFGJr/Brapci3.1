import { Component } from '@angular/core';
import { FormBuilder, FormControl, FormGroup, Validators } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
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
    private activatedRoute: ActivatedRoute,
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
  public evento: string = '';

  public event: Array<any> | any;
  public res: Array<any> | any;
  public parms: Array<any> | any;
  public sections: Array<any> | any;

  ngOnInit()
    {
         this.activatedRoute.params.subscribe(
          res=>
          {
            this.parms = res;
            console.log(this.parms.id)
            this.brapciService.getEvent(this.parms.id).subscribe(
              res=>{                
                this.event = res
                this.event = this.event.event   
                this.evento = this.event.id_e;
              }
            );
          }
        )              
    }


  cpf = new FormControl([]);
  data:Array<any> | any

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

                      /*********************** Lista Eventos */
                      this.brapciService.getSections(this.event.id_e,this.meuCPF.value.cpf).subscribe(
                        res=>{
                          console.log(res);
                          this.sections = res;
                          this.sections = this.sections.sections
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

    assign(id:string)
      {
        let ev:string = this.evento;
        let cpf:string = this.meuCPF.value.cpf;
        let sta:string = '1';
        this.brapciService.registerEV(id,cpf,sta,ev).subscribe(
          res=>{
            this.onSubmit()
          });
      }

    cancel(id:string)
      {
        if (confirm('Cancelar?'))
          {
            let cpf:string = this.meuCPF.value.cpf;
            this.brapciService.cancelEV(id,cpf).subscribe(
              res=>{
                this.onSubmit()
              });
          }
      }

    return()
      {
        this.ncpf = ''
        this.assignIn = ''
        this.router.navigate(['/'])
      }
}
