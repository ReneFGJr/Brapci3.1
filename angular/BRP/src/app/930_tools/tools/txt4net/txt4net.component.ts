import { Component } from '@angular/core';

@Component({
  selector: 'app-txt4net',
  templateUrl: './txt4net.component.html',
  styleUrls: ['./txt4net.component.scss'],
})
export class Txt4netComponent {
  public toolsName: string = 'TXT para .NET';
  public buttonName: string = 'Converter para .NET';
  public api_endpoint: string = 'tools/txt4net';
}
