import { Component } from '@angular/core';

@Component({
  selector: 'app-txt4unit2',
  templateUrl: './txt4unit2.component.html',
  styleUrls: ['./txt4unit2.component.scss'],
})
export class Txt4unit2Component {
  public toolsName: string = 'Extrair autores ou termos (Frequência)';
  public buttonName: string = 'Extrair Itens';
  public api_endpoint: string = 'tools/txt4unit2';
}
