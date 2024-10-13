import { Component } from '@angular/core';

@Component({
  selector: 'app-tools-icons',
  templateUrl: './tools-icons.component.html',
  styleUrls: ['./tools-icons.component.scss'],
})
export class ToolsIconsComponent {
  public serv = [
    { name: 'Convert TXT to .NET', url: 'txt4net', type: 'B' },
    { name: 'Extrair Autor/Assuntos (Alfabética)', url: 'txt4unit', type: 'B' },
    { name: 'Extrair Autor/Assuntos (Frequencia)', url: 'txt4unit2', type: 'B' },
    { name: 'Convert TXT to Matrix', url: 'txt4matrix', type: 'B' },
    { name: 'Convert RIS to MARC21', url: 'ris4marc', type: 'B' },

  ];

  public conv = [
    { name: 'Cálculo de amostra', url: 'amostra', type: 'T' },
    { name: 'Lei de Price (Autores)', url: 'price', type: 'T' },
    /* { name: 'Convert UTF8/ISO8859', url: 'txt4char', type: 'T' }, */
    /* { name: 'Troca Caracteres', url: 'txtChange', type: 'T' }, */
    /* { name: 'Convert .net para Gephi)', url: 'net4gephi', type: 'T' }, */
  ];

  public books = [{ name: 'Montar sumário', url: 'summary', type: 'T' }];
}
