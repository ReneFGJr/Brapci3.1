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
  public navbarClass = '';
  public pos: number = 0;

  constructor() {
    document.addEventListener('click', (clickEvent: MouseEvent) => {
      console.log('Click Event Details: ', clickEvent)
    })

    document.addEventListener('scroll', (scr: any) => {
      //console.log(document.documentElement.scrollTop);

      /* Troca do Menu superior */
      let posScreen = document.documentElement.scrollTop;
      this.pos = posScreen;

      if (posScreen > 0) {
        if (posScreen > 100) {
          this.fixed = 2;
          this.navbarClass = 'slideInDown';
        } else {
          if (this.fixed > 0)
            {
                this.navbarClass = 'slideOutUp';
            }
        }

      } else {
        this.fixed == 0
      }


    })
  }

  onScroll(): void {
    console.log("H")
    console.log(document.documentElement.scrollTop);
  }
}
