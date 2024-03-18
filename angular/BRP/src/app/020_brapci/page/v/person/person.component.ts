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
      type: 'doughnut', //this denotes tha type of chart
      // type: 'doughnut',
      options: {
        plugins: {
          legend: {
            display: false,
          },
        },
        aspectRatio: 2.5,
      },

      data: {
        // values on X-Axis
        labels: this.data.chart_coauthors.labels,
        datasets: [
          {
            label: 'Colaboração do Autor',
            data: this.data.chart_coauthors.total,
            hoverOffset: 4,
            backgroundColor: [
              '#000020',
              '#000040',
              '#000060',
              '#000080',
              '#0000A0',
              '#0000B0',
              '#0000C0',
              '#0000D0',
              '#0000E0',
              '#0000F0',
              '#0000F0',
              '#0000F0',
              '#0000F0',
              '#0000F0',
              '#0000F0',
              '#0000F0',
              '#0000F0',
              '#0000F0',
            ],
          },
        ],
      },
    });
  }
}
