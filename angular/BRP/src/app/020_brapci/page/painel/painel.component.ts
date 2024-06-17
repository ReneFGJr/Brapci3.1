import { Component } from '@angular/core';

@Component({
  selector: 'app-painel',
  templateUrl: './painel.component.html',
  styleUrls: ['./painel.component.scss'],
})
export class PainelComponent {
  public type: string = 'NA';
  public data: Array<any> | any;
  public sub: Array<any> | any;
  public chaves: Array<any> | any;
  public id: number = 0;
  public header = { title: 'Brapci - Painel' };
}
