import { Component } from '@angular/core';
import {
  FormArray,
  FormBuilder,
  FormControl,
  FormGroup,
  Validators,
} from '@angular/forms';
import { BrapciService } from '../../../000_core/010_services/brapci.service';

@Component({
  selector: 'app-search-brapci',
  templateUrl: './search-brapci.component.html',
  styleUrls: ['./search-brapci.component.scss'],
})
export class SearchBrapciComponent {
  public works: Array<any> | any;
  public totalw = 0;
  public result: Array<any> | any;
  public results: Array<any> | any;
  public filters: boolean = false;
  public advanceSearch: string = '';
  public term: string = '';
  public year_start: number = 1962;
  public year_end: number = new Date().getFullYear() + 1;
  public APIversion: string = '1';
  public loading: boolean = false;
  public loaging_img: string = '/assets/img/loading.svg';
  public class_filter: string = ''

  public msg_data_mark: string = 'Selecionar item para biblioteca pessoal';
  public msg_cover: string = 'Capa da publicação';
  public msg_noresult: string = 'Nenhum resultado encontrado!';
  public msg_result: string = 'resultado(s)';
  public msg_show: string = 'Mostrando';
  public msg_of: string = 'de';

  public marked: FormGroup;

  public yearsI: Array<any> = [];
  public yearsF: Array<any> = [];

  listArray: string[] = [];
  sum = 1;
  display = 5;
  direction = '';

  constructor(private fb: FormBuilder, private brapciService: BrapciService) {
    this.marked = this.fb.group({
      website: this.fb.array([], [Validators.required]),
    });
    let yearE = 2024
    let yearS = 1960;
    for (let i = yearS; i <= yearE; i++) {
      this.yearsI.push({ name: i });
    }
    for (let i = yearE; i >= yearS; i--) {
      this.yearsF.push({ name: i });
    }
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

  ngOnInit() {
    this.createForm();
  }

  clickFilters() {
    this.filters = !this.filters;

  }

  clickadvanceSearch() {
    console.log('Adcanced Search');
  }

  /**************************** MARK */
  markDOwn(e: any) {
    const website: FormArray = this.marked.get('website') as FormArray;

    if (e.target.checked) {
      website.push(new FormControl(e.target.value));
      console.log(e.target.value + ' on');
    } else {
      const index = website.controls.findIndex(
        (x) => x.value === e.target.value
      );
      console.log(e.target.value + ' off');
      website.removeAt(index);
    }
  }

  /**************************** SCROLL */
  onScrollDown(ev: any) {
    let max = 1;
    let ini: number = this.totalw;
    let fim: number = ini + max;
    let tot: number = this.results.length;
    if (fim > tot) {
      fim = tot;
    }

    for (let i = ini; i < fim; i++) {
      this.works.push(this.results[i]);
      this.totalw++;
    }
  }

  onScrollUp(ev: any) {}

  onSearch() {
    if (this.searchForm.valid) {
      let term = this.searchForm.value.term;
      this.loading = true;

      let dataS = this.searchForm.value.year_start;
      let dataF = this.searchForm.value.year_end;
      let dt:Array<any>|any = {di:dataS, df:dataF};

      this.brapciService.search(term, dt).subscribe((res) => {
        this.result = res;
        console.log(res);
        this.results = this.result.works;
        this.works = [];
        let max = 5;
        if (this.results.length < max) {
          max = this.results.length;
        }
        for (let i = 0; i < max; i++) {
          this.works.push(this.results[i]);
          this.totalw++;
        }
        this.loading = false;
      });
    } else {
      console.log('NÃO OK');
    }
  }
  onKeyPress() {}
}
