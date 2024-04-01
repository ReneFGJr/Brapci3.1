import { Component } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';

@Component({
  selector: 'app-price',
  templateUrl: './price.component.html',
})
export class PriceComponent {
  constructor(private fb: FormBuilder) {}
  myForm: FormGroup | any;
  amostra: number = 0;
  assimetria = '/assets/img/amostra_assimetria.png';

  ngOnInit() {
    this.myForm = this.fb.group({
      populacao: ['1000', Validators.required],
    });
  }

  onSubmit() {
    if (this.myForm.value['populacao'] != '') {
      let populacao = this.myForm.value['populacao'];
      this.amostra = Math.sqrt(populacao);
    }
  }
}
