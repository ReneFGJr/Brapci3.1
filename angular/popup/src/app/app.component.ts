import { Component } from '@angular/core';
import { FormBuilder, FormControl, FormGroup, Validators } from '@angular/forms';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.scss'],
})
export class AppComponent {
  busy: number = 0;
  search: any = '';
  title = 'popup';
  btn_new: string = 'false';
  btn_cancel: string = '';
  btn_aplly: string = 'false';

  constructor(private formBuilder: FormBuilder) {}
  name = new FormControl('', [Validators.required, Validators.maxLength(15)]);

  keyUp() {
    if (this.busy == 0) {
      this.busy = 1;
      this.search = this.name.value;

      if (this.search.length > 2) {
        console.log(this.search);
      }
      this.busy = 0;
    }
  }
}
