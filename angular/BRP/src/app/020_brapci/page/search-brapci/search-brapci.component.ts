import { Component } from '@angular/core';
import {
  FormArray,
  FormBuilder,
  FormControl,
  FormGroup,
  Validators,
} from '@angular/forms';
import { BrapciService } from '../../../000_core/010_services/brapci.service';
import { LocalStorageService } from '../../../000_core/010_services/local-storage.service';
import { map } from 'rxjs';
import { Router } from '@angular/router';

@Component({
  selector: 'app-search-brapci',
  templateUrl: './search-brapci.component.html',
  styleUrls: ['./search-brapci.component.scss'],
})
export class SearchBrapciComponent {
  public selected: number = 0;
  public works: Array<any> | any;
  public totalw: number = 0;
  public total: number = 0;
  public result: Array<any> | any;
  public results: Array<any> = [];
  public filters: boolean = false;
  public advanceSearch: string = '';
  public term: string = '';
  public year_start: number = 1962;
  public year_end: number = new Date().getFullYear() + 1;
  public APIversion: string = '1';
  public loading: boolean = false;
  public loaging_img: string = '/assets/img/loading.svg';
  public class_filter: string = '';
  private basket: Array<any> = [];

  public msg_data_mark: string = 'Selecionar item para biblioteca pessoal';
  public msg_cover: string = 'Capa da publicação';
  public msg_noresult: string = 'Nenhum resultado encontrado!';
  public msg_result: string = 'resultado(s)';
  public msg_show: string = 'Mostrando';
  public msg_of: string = 'de';

  public yearsI: Array<any> = [];
  public yearsF: Array<any> = [];

  list: any[];
  fields: any[];

  listArray: string[] = [];
  sum = 1;
  display = 5;
  direction = '';

  constructor(
    private fb: FormBuilder,
    private brapciService: BrapciService,
    private localStorageService: LocalStorageService,
    private router: Router
  ) {
    /************************************************************ Collection */
    this.list = [
      { name: 'Revistas Brasileiras', value: 'RA', checked: true },
      { name: 'Revistas Estrangeiras', value: 'RE', checked: true },
      { name: 'Eventos', value: 'EV', checked: true },
      { name: 'Livros e Capítulos de Livros', value: 'BK', checked: true },
    ];
    /************************************************************ Fields */
    this.fields = [
      { name: 'Título', value: 'TI', checked: true },
      { name: 'Resumo', value: 'AB', checked: true },
      { name: 'Palavras-chave', value: 'KW', checked: true },
      { name: 'Autor', value: 'AU', checked: true },
    ];
    /*********************************************************** BASKET */
    this.basket = this.localStorageService.get('marked');
    if (this.basket === null) {
      this.basket = [];
    }

    this.selected = this.basket.length;

    /*************************************************************** FILTRO ANO - STAND*/
    let yearE = 2024;
    let yearS = 1960;
    for (let i = yearS; i <= yearE; i++) {
      this.yearsI.push({ name: i });
    }
    for (let i = yearE; i >= yearS; i--) {
      this.yearsF.push({ name: i });
    }
  }

  public style: string = 'zoomIn';

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
    this.style = 'noshow';
  }

  clickFilters() {
    this.filters = !this.filters;
    if (this.filters) this.style = 'fadeIn show';
    else this.style = 'UP';
  }

  clickadvanceSearch() {
    this.router.navigate(['/search-adv']);
  }

  onSearch() {
    var map = new Map();
    if (this.searchForm.valid) {
      this.result = []
      this.results = []
      let term = this.searchForm.value.term;
      this.loading = true;

      let dataS = this.searchForm.value.year_start;
      let dataF = this.searchForm.value.year_end;
      let dt: Array<any> | any = { di: dataS, df: dataF };

      this.totalw = 0;

      this.brapciService.search(term, dt).subscribe((res) => {
        this.result = res;
        console.log(res)
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
        this.total = this.result.total;
        this.loading = false;
      });
    } else {
      console.log('NÃO OK');
    }
  }
  onKeyPress() {}
}
