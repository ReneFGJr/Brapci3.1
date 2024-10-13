import { Component } from '@angular/core';

@Component({
  selector: 'app-txt4unit',
  templateUrl: './txt4unit.component.html',
  styleUrls: ['./txt4unit.component.scss'],
})
export class Txt4unitComponent {
  public toolsName: string = 'Extrair autores ou termos';
  public buttonName: string = 'Extrair Itens';
  public api_endpoint: string = 'tools/txt4unit';
}
