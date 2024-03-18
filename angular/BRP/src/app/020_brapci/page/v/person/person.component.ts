import { Component, Input } from '@angular/core';
import Chart from 'chart.js/auto';

@Component({
  selector: 'app-brapci-person',
  templateUrl: './person.component.html',
  styleUrls: ['./person.component.scss'],
})
export class PersonComponent {
  @Input() public data: Array<any> | any;

  constructor() {}
  ngOnInit(): void {
    this.createChart();
  }

  public chart: any;
  //https://www.educative.io/answers/how-to-create-a-pie-chart-using-chartjs-in-angular
  createChart() {
    this.chart = new Chart('MyChart', {
      type: 'pie', //this denotes tha type of chart
      // type: 'doughnut',

      data: {
        // values on X-Axis
        labels: this.data.chart_coauthors.labels,
        datasets: [
          {
            label: 'A',
            data: [9168.2, 1417.8, 3335.1, 1165.0, 2078.9],
            hoverOffset: 4,
          },
        ],
      },
      options: { aspectRatio: 2.5 },
    });
  }
}
