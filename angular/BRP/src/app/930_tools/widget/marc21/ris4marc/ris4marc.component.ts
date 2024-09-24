import { BrapciService } from 'src/app/000_core/010_services/brapci.service';
import { Component } from '@angular/core';
import { FormBuilder, FormGroup } from '@angular/forms';

@Component({
  selector: 'app-ris4marc',
  templateUrl: './ris4marc.component.html',
})
export class Ris4marcComponent {
  myForm: FormGroup;
  result: string = '';
  data: Array<any> | any

  constructor(
    private fb: FormBuilder,
    private brapciService: BrapciService
    ) {
    this.myForm = this.fb.group({
      textInput: [''], // Campo de texto no formulário reativo
    });
  }

  onSubmit() {
    if (this.myForm.valid) {
      const textValue = this.myForm.get('textInput')?.value;

      let dt: Array<any> | any = { text: textValue };

      this.brapciService
        .api_post('tools/ris4marc', dt)
        .subscribe((res) => {
          console.log(res)
          this.data = res
          this.result = this.data['response'];
        });
      console.log('Texto enviado: ', textValue);
    }
  }
}
