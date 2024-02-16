import { HttpClient } from '@angular/common/http';
import { Component } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { BrapciService } from 'src/app/000_core/010_services/brapci.service';
import { LocalStorageService } from 'src/app/001_auth/service/local-storage.service';

@Component({
  selector: 'app-rdf-form',
  templateUrl: './rdf-form.component.html',
})
export class RdfFormComponent {
  public loading: boolean = false;
  public searchForm: FormGroup | any;
  public loaging_img: string = '/assets/img/loading.svg';
  public fields: any[] = [];
  public literal: boolean = false;
  public type: string = 'null';

  public result: Array<any> = [];
  public concepts: Array<any> = [];
  public sub: Array<any> | any;
  public selectedID: string = '';

  /**************** Params */
  public tclass: Array<any> | any = [];

  /*************** Inport */
  public propriety: string = 'hasAuthor'
  public class: string = 'Article'
  public xClass: string = ''
  public ID: string = '0'
  public text: string = ''

  /********************* BTN */
  public btn1: boolean = true;
  public btn2: boolean = true;
  public btn3: boolean = true;

  /******************** File */
  status: 'initial' | 'uploading' | 'success' | 'fail' = 'initial'; // Variable to store file status
  file: File | null = null; // Variable to store file

  constructor(
    private fb: FormBuilder,
    private brapciService: BrapciService,
    private localStorageService: LocalStorageService,
    private router: Router,
    private route: ActivatedRoute,
    private http: HttpClient
  ) {}

  // On file Select
  onChange(event: any) {
    const file: File = event.target.files[0];

    if (file) {
      this.status = 'initial';
      this.file = file;
    }
  }
  //https://uploadcare.com/blog/how-to-upload-files-in-angular/
  onUpload() {
    if (this.file) {
      const formData = new FormData();
      this.type = 'cover';

      if (this.propriety == 'hasFileStorage') {
        this.type = 'pdf';
      }
      console.log(this.propriety);
      console.log('+++' + this.type);
      let url = this.brapciService.url + 'upload/' + this.type + '/' + this.ID;
      //let url = 'http://brp/api/' + 'upload/' + this.type + '/' + this.ID
      console.log(url);

      formData.append('file', this.file, this.file.name);
      const upload$ = this.http.post(url, formData);
      this.status = 'uploading';

      upload$.subscribe({
        next: (x) => {
          console.log(x);
          this.status = 'success';
        },
        error: (error: any) => {
          this.status = 'fail';
          return error;
        },
      });
    }
  }

  createConcept() {
    let vlr = this.searchForm.value['term'];
    let dt = alert(vlr);
    alert('Created');
    let url = 'rdf/createConcept/' + this.xClass;
    let data: Array<any> | any = { name: vlr };
    console.log(url);
    this.brapciService.api_post(url, data).subscribe((res) => {
      console.log(res);
      this.submitAction();
    });
  }

  ngOnInit() {
    this.sub = this.route.params.subscribe((params) => {
      this.ID = params['id']; // (+) converts string 'id' to a number
      this.propriety = params['prop']; // (+) converts string 'id' to a number

      this.fields = [
        { name: 'Term', value: '', checked: true },
        { name: 'ID', value: this.ID, checked: true },
        { name: 'prop', value: this.propriety, checked: true },

        this.recoverResources(this.ID, this.propriety),
      ];

      this.createForm();
    });
  }

  term: string = '';
  select: Array<any> = [{ x: 'a' }, { y: 'b' }, { z: 'z' }];

  selectResource(ID: string) {
    this.selectedID = ID;
    this.btnChecks();
  }

  btnChecks() {
    this.btn1 = true;
    this.btn2 = true;
    this.btn3 = true;
    /************** Selecionado */
    if (this.selectedID != '') {
      this.btn2 = false;
      this.btn3 = false;
    }
    /************** Novo */
    if (this.selectedID == '') {
      if (this.concepts.length == 0) {
        this.btn1 = false;
      } else {
        /* Existe item na lista */
      }
    }
  }

  recoverResources(ID: string, prop: string) {
    let url = 'rdf/getResource';
    let data: Array<any> | any = { ID: ID, prop: prop };

    this.brapciService.api_post(url, data).subscribe((res) => {
      this.tclass = res;
      this.tclass = this.tclass['resource'];
      this.xClass = this.tclass[0]['Class'];
      if (this.xClass == 'Literal' || this.xClass == 'URL') {
        this.literal = true;
      }
      console.log('+++++++++++' + this.xClass);
    });
  }

  createForm() {
    this.searchForm = this.fb.group({
      term: [this.term, Validators.required],
      ID: this.ID,
      prop: this.propriety,
      text: this.text
    });
  }

  onSaveText()
    {
      let url = 'rdf/createLiteral';
      let q = this.searchForm.value['text'];
      alert('TEXTE - '+q)
      let ID = this.searchForm.value['ID'];
      let prop = this.searchForm.value['prop'];

      let data: Array<any> | any = { q: q, ID: ID, prop: prop };

      this.brapciService.api_post(url, data).subscribe((res) => {
        console.log(res);
      });

    }

  submitAction() {
    let url = 'rdf/searchSelect';
    let q = this.searchForm.value['term'];
    let ID = this.searchForm.value['ID'];
    let prop = this.searchForm.value['prop'];

    let data: Array<any> | any = { q: q, ID: ID, prop: prop };

    this.brapciService.api_post(url, data).subscribe((res) => {
      this.concepts = res;
      this.selectedID = '';
      this.btnChecks();
    });
  }

  save(close: boolean) {
    let url = 'rdf/dataAdd';
    let resource = this.selectedID;
    let source = this.searchForm.value['ID'];
    let prop = this.searchForm.value['prop'];
    let data: Array<any> | any = {
      source: source,
      prop: prop,
      resource: resource,
    };
    this.brapciService.api_post(url, data).subscribe((res) => {
      this.concepts = res;
      if (close) {
        this.wclose();
      } else {
        this.selectedID = '';
        this.btnChecks();
        window.opener.location.reload();
      }
    });
  }

  keyUp() {}

  wclose() {
    window.opener.location.reload();
    window.self.close();
  }
}
