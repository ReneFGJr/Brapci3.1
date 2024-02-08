import { Component } from '@angular/core';
import {
  FormArray,
  FormBuilder,
  FormControl,
  FormGroup,
  Validators,
} from '@angular/forms';
import { Router } from '@angular/router';
import { BrapciService } from 'src/app/000_core/010_services/brapci.service';
import { LocalStorageService } from 'src/app/001_auth/service/local-storage.service';

@Component({
  selector: 'popup-index',
  templateUrl: './index.component.html',
})
export class PopUpIndexComponent {
  public loading: boolean = false;
  public searchForm: FormGroup | any;
  public loaging_img: string = '/assets/img/loading.svg';
  public fields: any[];
  public literal: boolean = false;

  public result:Array<any> = []

  /*************** Inport */
  public propriety: string = 'hasAuthor';
  public class: string = 'Article';
  public ID: string = '12312';

  constructor(
    private fb: FormBuilder,
    private brapciService: BrapciService,
    private localStorageService: LocalStorageService,
    private router: Router
  ) {
    this.fields = [
      { name: 'Term', value: '', checked: true },
      { name: 'Select', value: [], checked: true },
    ];
  }

  ngOnInit() {
    this.createForm();
  }

  term: string = '';
  select: Array<any> = [{ x: 'a' }, { y: 'b' }];

  createForm() {
    this.searchForm = this.fb.group({
      term: [this.term, Validators.required],
      select: [this.select],
    });
  }

  submitAction() {
    let url = 'rdf/searchSelect/';
    let data = [
      { prop: this.propriety },
      { class: this.class },
      { ID: this.ID },
      { q: this.searchForm.value['term']}
    ];

    this.brapciService.api_post(url, data).subscribe((res) => {
      console.log(res)
      this.result = res;
    });
  }

  keyUp() {}

  onSearch() {
    alert('Search');
  }
}
