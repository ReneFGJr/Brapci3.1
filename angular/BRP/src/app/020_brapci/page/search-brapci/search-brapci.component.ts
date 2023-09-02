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
  public works:Array<any> | any;
  public totalw = 0
  public result:Array<any> | any;
  public results:Array<any> | any;
  public filters: boolean = false;
  public advanceSearch: string = '';
  public term: string = '';
  public year_start: number = 1962;
  public year_end: number = new Date().getFullYear() + 1;
  public APIversion: string = '1';
  public loading: boolean = false;
  public loaging_img:string = '/assets/img/loading.svg';
  
  listArray: string[] = [];
  sum = 1;
  display = 5;
  direction = "";  

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

  /**************************** SCROLL */
  onScrollDown(ev: any) {
    let max = 1;
    let ini:number = this.totalw;
    let fim:number = ini + max;
    let tot:number = this.results.length;
    if (fim > tot) { fim = tot; }

    for (let i = ini; i < fim; i++) {
      this.works.push(this.results[i]);
      this.totalw++;
    }
  }  

  onScrollUp(ev: any) {

  }

  onSearch() {
    if (this.searchForm.valid) {
      let term = this.searchForm.value.term;
      this.loading = true;
      this.brapciService.search(term).subscribe(
        res=>{
          this.result = res;
          this.results = this.result.works;
          this.works = [];
          let max = 5;
          if (this.results.length < max) 
            {
              max = this.results.length;
            }
          for (let i = 0; i < max; i++) {
            this.works.push(this.results[i]);
            this.totalw++;
          }
          this.loading = false;
        }
      );
    } else {
      console.log('NÃO OK');
    }
  }
  onKeyPress() {}
}
