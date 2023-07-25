import { Component } from '@angular/core';
import { FormControl } from '@angular/forms';

@Component({
  selector: 'app-cpf',
  templateUrl: './cpf.component.html',
  styleUrls: ['./cpf.component.scss']
})
export class CpfComponent {
  public message: string = '';
  public cpf:string = '';

  name = new FormControl([]);

  onSubmit()
    {
      alert("Submit")
    }
}
