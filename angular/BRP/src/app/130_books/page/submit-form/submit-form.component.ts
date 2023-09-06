import { BrapciService } from './../../../020_brapci/service/brapci.service';
import {
  FormBuilder,
  FormControl,
  FormGroup,
  Validators,
} from '@angular/forms';
import { Component } from '@angular/core';

@Component({
  selector: 'app-book-submit-form',
  templateUrl: './submit-form.component.html',
  styleUrls: ['./submit-form.component.scss'],
})
export class BookSubmitFormComponent {
  //public usrNameChanges: string = '';
  //public usrNameStatus: string = '';
  public userForm: FormGroup;

  constructor(fb: FormBuilder) {
    this.userForm = fb.group({
      name: new FormControl('Mahesh', Validators.maxLength(10)),
      age: new FormControl(20, Validators.required),
      city: new FormControl(),
      country: new FormControl(),
    });
  }
  get userName(): any {
    return this.userForm.get('name');
  }
  onFormSubmit(): void {}
  setDefaultValue() {
    this.userForm.setValue({ name: 'Mahesh', age: 20, city: '', country: '' });
  }
}
