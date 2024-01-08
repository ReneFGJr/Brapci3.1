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
    this.style = 'noshow';
  }

  clickSearch() {
    this.router.navigate(['/']);
  }

  onSearch() {}
}
