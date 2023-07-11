import { Component, EventEmitter } from '@angular/core';

@Component({
  selector: 'app-navbar',
  templateUrl: './navbar.component.html',
  styleUrls: ['./navbar.component.scss']
})


export class NavbarComponent {

  public event: any | any;

  public name = 'Hello';

  constructor() {
    document.addEventListener('click', (clickEvent: MouseEvent) => {
      console.log('Click Event Details: ', clickEvent)
    })

    document.addEventListener('scroll', (scr: any) => {
      console.log(document.documentElement.scrollTop);
    })
  }

    onScroll(): void {
      console.log("H")
      console.log(document.documentElement.scrollTop);
    }
}
