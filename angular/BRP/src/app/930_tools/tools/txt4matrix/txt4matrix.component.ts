import { Component } from '@angular/core';

@Component({
  selector: 'app-txt4matrix',
  templateUrl: './txt4matrix.component.html',
  styleUrls: ['./txt4matrix.component.scss'],
})
export class Txt4matrixComponent {
  public toolsName: string = 'TXT para Matriz';
  public buttonName: string = 'Converter TXT para Matriz';
  public api_endpoint: string = 'tools/txt4matrix';
}
