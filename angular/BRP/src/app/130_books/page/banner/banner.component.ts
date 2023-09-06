import { Component } from '@angular/core';

@Component({
  selector: 'app-books-banner-home',
  templateUrl: './banner.component.html',
  styleUrls: ['./banner.component.scss']
})
export class BooksBannerHomeComponent {
  ngOnInit()
    {
      console.log("HELLO Banner")
    }

}
