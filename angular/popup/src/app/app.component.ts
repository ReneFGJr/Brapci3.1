import { Component } from '@angular/core';
import { FormBuilder, FormControl, FormGroup, Validators } from '@angular/forms';
import { ApiService } from './brapci/api.service';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.scss'],
})
export class AppComponent {
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
    private apiService: ApiService
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
        console.log("==>"+this.search);
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
