import { Component } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { BrapciService } from 'src/app/000_core/010_services/brapci.service';
import { environment } from 'src/environments/environment';


@Component({
  selector: 'app-rdf-edit',
  templateUrl: './edit.component.html',
})
export class EditRDFComponent {
  public type: string = 'NA';
  public data: Array<any> | any;
  public group: Array<any> | any;
  public sub: Array<any> | any;
  public chaves: Array<any> | any;
  public id: number = 0;
  public header = { title: 'Brapci' };

  constructor(
    private brapciService: BrapciService,
    private route: ActivatedRoute,
    private router: Router
  ) {}

  ngOnInit() {
    this.sub = this.route.params.subscribe((params) => {
      this.id = +params['id']; // (+) converts string 'id' to a number
      let url = 'rdf/a/' + this.id;
      this.brapciService.api_post(url).subscribe((res) => {
        this.data = res;
        this.group = this.data.groups;
        console.log(this.data);
      });
    });
  }

  delete(id: number) {
    // URL da página que você quer abrir
    //const url = 'https://brapci.inf.br/#/popup/rdf/delete/' + id + '/del';
    const url = environment.path + '/#/popup/rdf/delete/' + id + '/del';

    // Defina as opções para a nova janela
    const windowFeatures =
      'toolbar=no, menubar=no, width=800, height=430, top=100, left=100';

    // Abra a nova janela/popup
    window.open(url, '_blank', windowFeatures);
  }

  popup(id: number, prop: string) {
    // URL da página que você quer abrir
    const url = 'https://brapci.inf.br/#/popup/rdf/add/' + id + '/' + prop;

    // Defina as opções para a nova janela
    const windowFeatures =
      'toolbar=no, menubar=no, width=800, height=430, top=100, left=100';

    // Abra a nova janela/popup
    window.open(url, '_blank', windowFeatures);
  }
}
