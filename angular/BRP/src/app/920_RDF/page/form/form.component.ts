import { Component } from '@angular/core';
import { FormBuilder, FormControl, Validators } from '@angular/forms';
import { BrapciService } from 'src/app/000_core/010_services/brapci.service';

@Component({
  selector: 'app-form',
  templateUrl: './form.component.html',
  styleUrls: ['./form.component.scss']
})
export class FormComponent {
  busy: number = 0;
  search: any = ''
  title = 'popup'
  prop: string = ''
  term:string = ''
  btn_new: string = 'false'
  btn_cancel: string = ''
  btn_aplly: string = 'false'

  Concepts:Array<any> = []

  constructor(
    private formBuilder: FormBuilder,
    private apiService: BrapciService
    ) {}
  name = new FormControl('', [Validators.required, Validators.maxLength(15)]);

  setTerm(id:string) {
    this.term = id;
  }

  keyUp() {
    console.log(this.busy)
    if (this.busy == 0) {
      this.search = this.name.value;
      console.log('==>' + this.search.length);
      if (this.search.length > 2) {
        this.busy = 1;
        console.log("=LOG=>"+this.search);
        this.apiService.api(1, this.search, this.prop).subscribe(
          (res) => {
            this.Concepts = res
            this.Concepts = this.Concepts
            console.log("+===================")
            console.log(this.Concepts);
            this.busy = 0;
          }
        );

      }
    }
  }
}
