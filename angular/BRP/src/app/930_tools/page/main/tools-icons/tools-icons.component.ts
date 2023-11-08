import { Component } from '@angular/core';

@Component({
  selector: 'app-tools-icons',
  templateUrl: './tools-icons.component.html',
  styleUrls: ['./tools-icons.component.scss'],
})
export class ToolsIconsComponent {
  public serv = [
    { name: 'Convert TXT to .NET', url: 'tools/txt4net', type: 'B' },
    { name: 'Convert TXT to Matrix', url: 'tools/txt4matrix', type: 'B' },
    { name: 'Convert UTF8/ISO8859', url: 'tools/txt4char', type: 'T' },
    { name: 'Troca Caracteres', url: 'tools/txtChange', type: 'T' },
    { name: 'Convert .net para Gephi)', url: 'tools/net4gephi', type: 'T' },
  ];
}
