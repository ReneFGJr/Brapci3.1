import { Component } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { BrapciService } from 'src/app/000_core/010_services/brapci.service';
import { LocalStorageService } from 'src/app/001_auth/service/local-storage.service';

@Component({
  selector: 'app-rdf-form',
  templateUrl: './rdf-form.component.html',
  styleUrls: ['./rdf-form.component.scss'],
})
export class RdfFormComponent {
  public loading: boolean = false;
  public searchForm: FormGroup | any;
  public loaging_img: string = '/assets/img/loading.svg';
  public fields: any[] = [];
  public literal: boolean = false;

  public result: Array<any> = [];
  public sub: Array<any> | any;

  /*************** Inport */
  public propriety: string = 'hasAuthor';
  public class: string = 'Article';
  public ID: string = '0';

  /********************* BTN */
  public btn1: boolean = true;
  public btn2: boolean = true;
  public btn3: boolean = true;

  constructor(
    private fb: FormBuilder,
    private brapciService: BrapciService,
    private localStorageService: LocalStorageService,
    private router: Router,
    private route: ActivatedRoute
  ) {}

  ngOnInit() {
    this.sub = this.route.params.subscribe((params) => {
      this.ID = params['id']; // (+) converts string 'id' to a number
      this.propriety = params['prop']; // (+) converts string 'id' to a number

      this.fields = [
        { name: 'Term', value: '', checked: true },
        { name: 'ID', value: this.ID, checked: true },
        { name: 'prop', value: this.propriety, checked: true },
      ];

      this.createForm();
    });
  }

  term: string = '';
  select: Array<any> = [{ x: 'a' }, { y: 'b' }, {z:'z'}];

  createForm() {
    this.searchForm = this.fb.group({
      term: [this.term, Validators.required],
      ID: this.ID,
      prop: this.propriety,
    });
  }

  submitAction() {
    let url = 'rdf/searchSelect';
    let data = this.searchForm.value;

    //this.brapciService.RDFapi(url,ID,prop,q).subscribe((res) => {
      this.brapciService.api_post(url, data).subscribe((res) => {
        console.log('===================');
        console.log(res);
        this.result = res;
      });
  }

  keyUp() {}

  onSearch() {
    alert('Search');
  }

  wclose()
    {
      alert("CLOSE")
    }
}
