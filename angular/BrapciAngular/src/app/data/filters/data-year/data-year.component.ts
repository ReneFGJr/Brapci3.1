import { Component } from '@angular/core';

@Component({
  selector: 'app-data-year',
  templateUrl: './data-year.component.html',
  styleUrls: ['./data-year.component.scss']
})

export class DataYearComponent {
  public yearI: Array<{ year: number }> = [];
  public yearF: Array<{ year: number }> = [];

  public yearStart = 1960;
  public yearEnd = (new Date()).getFullYear() + 1;

  constructor() {
    let f: number = (new Date()).getFullYear() + 1;
    let i: number = 1960;
    while (i < f) {
      this.yearI.push({ year: i });
      i++;
    }

    while (i > 1959) {
      this.yearF.push({ year: i });
      i--;
    }
  }

  selectYear(e: any, tp: number) {
    console.log(e, tp);

  }
}
