import { Component } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Router } from '@angular/router';

@Component({
  selector: 'app-search-brapci-adv',
  templateUrl: './search-brapci-adv.component.html',
})
export class SearchBrapciAdvComponent {
  header: string = 'Busca avançada';
  public year_start: number = 1962;
  public year_end: number = new Date().getFullYear() + 1;
  public term: Array<any> = [];
  public APIversion: String = 'a2';
  public searchForm: FormGroup | any;
  public loading: boolean = false;
  public style: string = '';
  public logo: string = '/assets/img/brand_brapci_shadown.png';
  public form: Array<any> = [];
  public field: Array<any> = [{ op: 'AND', type: '*', q: '' }];

  public optionsType: Array<any> = [
    { id: 0, name: 'Todos os campos' },
    { id: 1, name: 'Título' },
    { id: 2, name: 'Resumo' },
    { id: 3, name: 'Palavras-chave' },
    { id: 4, name: 'Autor' },
  ]

  public operadorType: Array<any> = [
    { id: 'AND', name: "AND" },
    { id: 'OR', name: "OR" },
    { id: 'NOT', name: "NOT" }
  ]

  public loaging_img: string = '/assets/img/loading.svg';

  constructor(private fb: FormBuilder, public router: Router) {}

  createForm() {
    this.searchForm = this.fb.group({
      term: [this.term, Validators.required],
      year_start: [this.year_start, Validators.required],
      year_end: [this.year_end, Validators.required],
      api_version: [this.APIversion, Validators.required],
    });
  }

  ngOnInit() {
    this.createForm();
    this.form.push(this.field);
  }

  newField() {
    this.form.push(this.field);
  }

  onType(pos: string, id: number = 0) {
    this.form[id][0].type = pos;
  }

  onBoolean(pos: string, id: number = 0) {
    this.form[id][0].op = pos;
  }

  clickSearchBasic() {
    this.router.navigate(['/']);
  }

  onSearch() {}
}
