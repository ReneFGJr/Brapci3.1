import { Component } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { BrapciService } from 'src/app/000_core/010_services/brapci.service';

@Component({
  selector: 'app-indexs',
  templateUrl: './indexs.component.html',
})
export class IndexsComponent {
  public header: string = 'Índices';
  public type = '';
  public data: Array<any> | any;
  public ltr: string = '';
  public sub: Array<any> | any;
  public typeName: string = '';
  public langs: Array<any> = ['pt', 'en', 'es'];
  public lang: string = '';

  public ltrs: Array<any> = [
    'A',
    'B',
    'C',
    'D',
    'E',
    'F',
    'G',
    'H',
    'I',
    'J',
    'K',
    'L',
    'M',
    'N',
    'O',
    'P',
    'Q',
    'R',
    'S',
    'T',
    'U',
    'V',
    'X',
    'Y',
    'W',
    'Z',
  ];

  constructor(
    public brapciService: BrapciService,
    private route: ActivatedRoute
  ) {}

  ngOnChange() {
    console.log('NEW');
  }

  getLang(lg: string) {
    this.lang = lg;
  }
  getTerms(ltr: string) {
    this.ltr = ltr;
    this.brapciService
      .api_post('indexs/' + this.type + '/' + this.ltr)
      .subscribe((res) => {
        this.data = res;
      });
  }

  ngOnInit() {
    this.sub = this.route.params.subscribe((params) => {
      //this.id = +params['id']; // (+) converts string 'id' to a number
      this.ltr = params['id']; // (+) converts string 'id' to a number
      this.type = params['type']; // (+) converts string 'id' to a number
      if (this.type == 'author')
        {
          this.typeName = 'de Autores';
        }
      if (this.type == 'subject') {
        this.typeName = 'de Assuntos';
      }
      this.getTerms(this.ltr);
    });
  }
}
