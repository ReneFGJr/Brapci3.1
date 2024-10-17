import { Component } from '@angular/core';
import { FormBuilder, FormGroup } from '@angular/forms';

@Component({
  selector: 'app-search-simple-form',
  templateUrl: './search-simple-form.component.html',
  styleUrls: ['./search-simple-form.component.scss'],
})
export class SearchSimpleFormComponent {
  searchForm: FormGroup;

  constructor(private fb: FormBuilder) {
    this.searchForm = this.fb.group({
      searchTerm: [''],
      year: [''],
      gt: [''],
      workType: [''],
    });
  }

  onSubmit() {
    const filters = this.searchForm.value;
    console.log(filters); // Aqui você pode fazer a lógica de busca ou enviar os dados para um serviço
  }
}
