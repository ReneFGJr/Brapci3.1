import { Component, EventEmitter } from '@angular/core';
import { NgOptimizedImage } from '@angular/common'

@Component({
  selector: 'app-navbar',
  templateUrl: './navbar.component.html',
  styleUrls: ['./navbar.component.scss']
})


export class NavbarComponent {

  public event: any | any;

  public fixed: number = 1;

  constructor() {
    document.addEventListener('click', (clickEvent: MouseEvent) => {
      console.log('Click Event Details: ', clickEvent)
    })

    document.addEventListener('scroll', (scr: any) => {
      //console.log(document.documentElement.scrollTop);

      /* Troca do Menu superior */
      let posScreen = document.documentElement.scrollTop;
      if ((posScreen > 0) || (this.fixed == 0)) {
        if (posScreen > 100)
          {
            this.fixed = 2;
          } else {
            this.fixed = 1;
          }

      } else {
        if ((posScreen == 0) || (this.fixed == 1)) {
          this.fixed = 0;
        }
      }
    })
  }

    onScroll(): void {
      console.log("H")
      console.log(document.documentElement.scrollTop);
    }
}
