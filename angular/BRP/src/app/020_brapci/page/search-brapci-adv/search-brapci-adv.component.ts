import { Component } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { BrapciService } from 'src/app/000_core/010_services/brapci.service';

@Component({
  selector: 'app-search-brapci-adv',
  templateUrl: './search-brapci-adv.component.html',
})
export class SearchBrapciAdvComponent {
  header: string = 'Busca avançada'
  public year_start: number = 1962
  public year_end: number = new Date().getFullYear() + 1
  public term: Array<any> = []
  public APIversion: String = 'a2'
  public searchForm: FormGroup | any
  public loading: boolean = false
  public style: string = ''
  public logo: string = '/assets/img/brand_brapci_shadown.png'
  public form: Array<any> = []
  public field: Array<any> = []

  public result: Array<any> = []
  public results: Array<any> = []
  public totalw:number = 0
  public total:number = 0
  public works: Array<any> = [];

  public optionsType: Array<any> = [
    { id: 0, name: 'Todos os campos' },
    { id: 1, name: 'Título' },
    { id: 2, name: 'Resumo' },
    { id: 3, name: 'Palavras-chave' },
    { id: 4, name: 'Autor' },
  ];

  public operadorType: Array<any> = [
    { id: 1, name: 'AND' },
    { id: 2, name: 'OR' },
    { id: 3, name: 'NOT' },
  ];

  public loaging_img: string = '/assets/img/loading.svg';

  constructor(
    private fb: FormBuilder,
    public router: Router,
    public brapciService: BrapciService
    ) {}

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
    this.newField();
  }

  newField() {
    let tp = [{ name: '', op: 0, type: 0 }];

    this.form.push(tp);
  }

  pressKeyUp(txt: string, id: number) {
    this.form[id][0].name = txt;
  }

  onType(pos: string, id: number = 0) {
    this.form[id][0].type = pos;
  }

  onBoolean(tpt: string, id: number = 0) {
    this.form[id][0].op = tpt;
    console.log(this.form[0]);
  }

  clickSearchBasic() {
    this.router.navigate(['/']);
  }

  onAdvancedSearch()
    {
      var map = new Map();
      console.log(this.searchForm.valid);
        this.result = []
        this.results = []
        let term = this.searchForm.value.term;
        this.loading = true;

        let dataS = this.searchForm.value.year_start;
        let dataF = this.searchForm.value.year_end;
        let dt: Array<any> | any = { di: dataS, df: dataF };

        this.totalw = 0;

        this.brapciService.searchAdv(this.form).subscribe((res) => {
          this.result = res;
          console.log(res)
          /*
          //this.results = this.result.works;
          this.works = [];
          let max = 5;
          if (this.results.length < max) {
            max = this.results.length;
          }
          for (let i = 0; i < max; i++) {
            this.works.push(this.results[i]);
            this.totalw++;
          }
          //this.total = this.result.total;
          */
          this.loading = false;
        });
    }
  onSearch() {}
}
