import { Component } from '@angular/core';

@Component({
  selector: 'app-brapci-welcome',
  templateUrl: './welcome.component.html',
  styleUrls: ['./welcome.component.scss'],
})
export class BrapciWelcomeComponent {
  public title: string = '';
  //public logo: string = '/assets/img/logo_brapci_wire_blue.png'
  public logo: string = '/assets/img/brand_brapci_shadown.png';
}
