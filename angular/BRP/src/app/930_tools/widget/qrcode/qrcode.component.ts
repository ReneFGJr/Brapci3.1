import { Component } from '@angular/core';

@Component({
  selector: 'app-qrcode',
  templateUrl: './qrcode.component.html',
})
export class QrcodeComponent {
  public url:string = 'https://brapci.inf.br/'
  public size:number = 300
}
