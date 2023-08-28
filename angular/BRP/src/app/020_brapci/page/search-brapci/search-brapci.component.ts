import { Component } from '@angular/core';
import {
  FormBuilder,
  FormGroup,
  Validators,
} from '@angular/forms';
import { BrapciService } from '../../service/brapci.service';

@Component({
  selector: 'app-search-brapci',
  templateUrl: './search-brapci.component.html',
  styleUrls: ['./search-brapci.component.scss'],
})
export class SearchBrapciComponent {
  public result:Array<any> | any;
  public filters: boolean = false;
  public advanceSearch: string = '';
  public term: string = '';
  public year_start: number = 1962;
  public year_end: number = new Date().getFullYear() + 1;
  public APIversion: string = '1';

  constructor(
      private fb: FormBuilder,
      private brapciService:BrapciService
    ) {}

  searchForm: FormGroup | any;
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
  }

  clickFilters() {
    this.filters = !this.filters;
  }

  clickadvanceSearch() {
    console.log("Adcanced Search")
  }

  onSearch() {
    if (this.searchForm.valid) {
      console.log('OK');
      console.log(this.searchForm.value.term);
      let term = this.searchForm.value.term;
      this.brapciService.search(term).subscribe(
        res=>{
          this.result = res;
          console.log(res);
        }
      );
    } else {
      console.log('NÃO OK');
    }
  }
  onKeyPress() {}
}
