import { Component } from '@angular/core';

@Component({
  selector: 'app-theme-footer',
  templateUrl: './theme-footer.component.html',
  styleUrls: ['./theme-footer.component.scss'],
})
export class ThemeFooterComponent {
  public brapci_data: number = 2010;

  ngOnInit() {
    this.brapci_data = new Date().getFullYear();
    console.log(this.brapci_data); // output 2020
  }
}
