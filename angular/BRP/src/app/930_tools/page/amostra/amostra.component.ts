import { Component } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';

@Component({
  selector: 'app-amostra',
  templateUrl: './amostra.component.html',
})
export class AmostraComponent {
  constructor(private fb: FormBuilder) {}

  myForm: FormGroup | any;
  confianca_niveis: Array<any> = ['99%','95%', '90%'];

  amostra: number = 0;
  z: number = 0;

  ngOnInit() {
    this.myForm = this.fb.group({
      populacao: ['5000', Validators.required],
      erro_amostral: [5],
      confianca: [95, Validators.required],
      distribuicao: ['50/50'],
    });
  }

  onSubmit() {
    let pop = this.myForm.value['populacao'];
    let cnf = this.myForm.value['confianca'];
    let e = this.myForm.value['erro_amostral'] / 100;
;
    /* Margens de Confiança *****************************************************
        90 - 1,645
        95 - 1,96
        99 - 2,575
    */

    if (cnf == '95%') {
      this.z = 1.96;
    } else if (cnf == '90%') {
      this.z = 1.645;
    } else if (cnf == '99%') {
      this.z = 2.575;
    }
    let z = this.z
    let p = 0.5;
    let vlr = (p * (1 - p) * z * z) / (e * e)
    let n = vlr / (1 + (vlr - 1) / pop);
    this.amostra = n
  }
}
