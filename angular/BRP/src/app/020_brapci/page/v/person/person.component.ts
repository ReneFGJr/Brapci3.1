import { Component, ElementRef, Input, ViewChild } from '@angular/core';
import Chart from 'chart.js/auto';

@Component({
  selector: 'app-brapci-person',
  templateUrl: './person.component.html',
})
export class PersonComponent {
  @Input() public data: Array<any> | any
  public dataTAG:Array<any>|any

  constructor() {}
  ngOnInit(): void {}

  ngOnChanges(): void {
    if (this.chart != undefined) {
      this.chart.destroy();
    }
    if (this.production != undefined) {
      this.production.destroy();
    }

    this.createChart();
    this.createProduction();

    this.dataTAG = this.data.dataTAG;
  }

  public chart: any;
  public production: any;

  createProduction() {
    this.production = new Chart('MyProduction', {
      type: 'bar', //this denotes tha type of chart

      data: {
        // values on X-Axis
        labels: this.data.chart_years.labels,

        datasets: [
          {
            label: 'Article',
            data: this.data.chart_years.data.Article,
            backgroundColor: 'blue',
          },
          {
            label: 'Proceeding',
            data: this.data.chart_years.data.Proceeding,
            backgroundColor: 'limegreen',
          },
          {
            label: 'Book',
            data: this.data.chart_years.data.Book,
            backgroundColor: 'brown',
          },
          {
            label: 'BookChapter',
            data: this.data.chart_years.data.BookChapter,
            backgroundColor: 'red',
          },
        ],
      },
      options: {
        plugins: {
          title: {
            display: false,
            text: '',
          },
        },
        scales: {
          x: {
            stacked: true,
          },
          y: {
            stacked: true,
          },
        },
        aspectRatio: 2.5,
      },
    });
  }

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

  onWorkClick() {

  }
}
