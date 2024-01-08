import { Component, Input } from '@angular/core';
import { LocalStorageService } from '../../../000_core/010_services/local-storage.service';
import { map } from 'rxjs';
import {
  FormArray,
  FormBuilder,
  FormControl,
  FormGroup,
  Validators,
} from '@angular/forms';
@Component({
  selector: 'app-search-result',
  templateUrl: './search-result.component.html',
})
export class SearchResultComponent {
  @Input() public results: Array<any> | any;
  @Input() public result: Array<any> | any;

  public APIversion: string = '1';
  public loading: boolean = false;
  public loaging_img: string = '/assets/img/loading.svg';
  public class_filter: string = '';
  private basket: Array<any> = [];
  public marked: FormGroup;

  /******************************************************* Constructor */
  constructor(
    private fb: FormBuilder,
    private localStorageService: LocalStorageService
  ) {
    this.marked = this.fb.group({});
    this.selected = this.basket.length;

    /*********************************************************** MARKED */
    this.marked = this.fb.group({
      website: this.fb.array([], [Validators.required]),
    });

    this.selected = this.basket.length;
    /*********************************************************** BASKET */
    this.basket = this.localStorageService.get('marked');
    if (this.basket === null) {
      this.basket = [];
    }
  }

  public selected: number = 0;
  public works: Array<any> | any;
  public totalw: number = 0;
  public total: number = 0;
  public filters: boolean = false;
  public advanceSearch: string = '';
  public term: string = '';

  /********************** Tradução */
  public msg_data_mark: string = 'Selecionar item para biblioteca pessoal';
  public msg_cover: string = 'Capa da publicação';
  public msg_noresult: string = 'Nenhum resultado encontrado!';
  public msg_result: string = 'resultado(s)';
  public msg_show: string = 'Mostrando';
  public msg_of: string = 'de';

  ngOnChanges() {
    this.works = [];
    let max = 5;
    if (this.results.length < max) {
      max = this.results.length;
    }
    for (let i = 0; i < max; i++) {
      this.works.push(this.results[i]);
      this.totalw++;
    }
  }

  /**************************** MARK */
  markDOwn(e: any) {
    let id = 'mk' + e;
    let checkbox = document.getElementById(id) as HTMLInputElement | null;

    const wb: FormArray = this.marked.get('website') as FormArray;

    if (e.target.checked) {
      /********* Verifica se existe */
      const index = wb.controls.findIndex((x) => x.value === e.target.value);
      if (index > 0) {
      } else {
        wb.push(new FormControl(e.target.value));
      }
    } else {
      const index = wb.controls.findIndex((x) => x.value === e.target.value);
      wb.removeAt(index);
    }
    this.localStorageService.set('marked', wb.value);
    this.basket = wb.value;
    this.selected = this.basket.length;
  }

  updateBasket(e: string) {
    let it = this.basket;
    const wb: FormArray = this.marked.get('website') as FormArray;

    if (it != null) {
      it.map((idx: string) => {
        let xid = wb.controls.findIndex((x) => x.value === idx);
        wb.removeAt(xid);
      });
    }

    it.map((idx: string) => {
      let id = 'mk' + idx;
      let checkbox = document.getElementById(id) as HTMLInputElement | null;
      if (checkbox != null) {
        checkbox.checked = false;
      }
    });

    this.basket = [];
    this.localStorageService.remove('mark');
    this.selected = 0;
  }

  checked(id: string) {
    if (this.basket.includes(id)) {
      return true;
    } else {
      return false;
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

  onKeyPress() {}
}
