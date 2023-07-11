import { Component, EventEmitter } from '@angular/core';

@Component({
  selector: 'app-navbar',
  templateUrl: './navbar.component.html',
  styleUrls: ['./navbar.component.scss']
})


export class NavbarComponent {

  public event: any | any;

  public fixed = false;

  constructor() {
    document.addEventListener('click', (clickEvent: MouseEvent) => {
      console.log('Click Event Details: ', clickEvent)
    })

    document.addEventListener('scroll', (scr: any) => {
      console.log(document.documentElement.scrollTop);

      /* Troca do Menu superior */
      let posScreen = document.documentElement.scrollTop;
      if ((posScreen > 100) || (this.fixed == false)) {
        this.fixed = true;
      } else {
        if (posScreen == 0) {
          this.fixed = false;
        }
      }
    })
  }

    onScroll(): void {
      console.log("H")
      console.log(document.documentElement.scrollTop);
    }
}
