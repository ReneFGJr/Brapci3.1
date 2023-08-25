import { Component } from '@angular/core';
import { FormBuilder, FormControl, FormGroup, Validators } from '@angular/forms';

@Component({
  selector: 'app-search-brapci',
  templateUrl: './search-brapci.component.html',
  styleUrls: ['./search-brapci.component.scss']
})
export class SearchBrapciComponent {
  public filters:boolean = false;
  public term: string = '';
  public year_start: number = 1962;
  public year_end: number = new Date().getFullYear()+1;
  public APIversion: string = '1';

  constructor(
    private fb: FormBuilder,
  ) {}

  ngOnInit() {
    this.createForm();
  }

  clickFilters()
    {
      this.filters = !this.filters
    }

  searchForm: FormGroup | any;
  createForm() {
    this.searchForm = this.fb.group({
      term: [this.term, Validators.required],
      year_start: [this.year_start, Validators.required],
      year_end: [this.year_end, Validators.required],
      api_version: [this.APIversion, Validators.required],
    });
  }

  onSearch() {
    if (this.searchForm.valid)
      {
        console.log("OK");
        let term = this.searchForm.value.term;
        //this.bannerComponent.search(term);
      } else {
        console.log("NÃO OK");
      }
  }
  onKeyPress() {}
}
