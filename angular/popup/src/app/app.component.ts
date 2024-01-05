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
  term:string = ''
  btn_new: string = 'false'
  btn_cancel: string = ''
  btn_aplly: string = 'false'

  Concpets:Array<any> = [
    {Name:'Hello World',id:'1'},
    {Name:'RDF',id:'2'}
  ]

  constructor(
    private formBuilder: FormBuilder,
    private apiService: ApiService
    ) {}
  name = new FormControl('', [Validators.required, Validators.maxLength(15)]);

  setTerm(id:string) {
    this.term = id;
  }

  keyUp() {
    if (this.busy == 0) {
      this.busy = 1;
      this.search = this.name.value;

      if (this.search.length > 2) {
        console.log(this.search);
        this.apiService.api(1,this.search);
      }
      this.busy = 0;
    }
  }
}
