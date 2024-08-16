import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-banner-brapci-livros',
  templateUrl: './banner-brapci-livros.component.html',
  styleUrls: ['./banner-brapci-livros.component.scss']
})
export class BannerBrapciLivrosComponent {
  @Input() public title:string = ''
}
