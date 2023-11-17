import { Component } from '@angular/core';

@Component({
  selector: 'app-tools-icons',
  templateUrl: './tools-icons.component.html',
  styleUrls: ['./tools-icons.component.scss'],
})
export class ToolsIconsComponent {
  public serv = [
    { name: 'Convert TXT to .NET', url: 'txt4net', type: 'B' },
    { name: 'Convert TXT to Matrix', url: 'txt4matrix', type: 'B' },
  ];

  public conv = [
    { name: 'Convert UTF8/ISO8859', url: 'txt4char', type: 'T' },
    { name: 'Troca Caracteres', url: 'txtChange', type: 'T' },
    { name: 'Convert .net para Gephi)', url: 'net4gephi', type: 'T' },
  ];
}
