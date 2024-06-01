import { BrapciService } from 'src/app/000_core/010_services/brapci.service';
import { Component } from '@angular/core';
import Chart, { ChartDataset } from 'chart.js/auto';

interface BolsaData {
  [key: string]: { [key: string]: number };
}

@Component({
  selector: 'app-pq-ano-bolsa',
  templateUrl: './pq-ano-bolsa.component.html',
})
export class PqAnoBolsaComponent {
  public data: Array<any> | any;
  public barChartOptions = {
    scaleShowVerticalLines: false,
    responsive: true,
  };

  public production: any;
  public table_label: Array<any> | any

  constructor(public brapciService: BrapciService) {}

  ngOnInit() {
    this.brapciService.api_post('pq/bolsa_ano_tipo').subscribe((res) => {
      this.data = res;
      this.getTipoKeys()
      this.createChart()
      console.log(this.data.data['2']);
    });
  }

  getTipoKeys(): string[] {
    this.table_label = Object.keys(this.data.data);
    return this.table_label
  }

  createChart() {
    this.production = new Chart('MyProduction', {
      type: 'bar', //this denotes tha type of chart

      data: {
        // values on X-Axis
        labels: this.data.label,

        datasets: [
          {
            label: 'PQ2',
            data: this.data.data['2'],
            backgroundColor: '#ff9900',
          },
          {
            label: 'PQ2C',
            data: this.data.data['2C'],
            backgroundColor: '#b26b2e',
          },
          {
            label: 'PQ2B',
            data: this.data.data['2B'],
            backgroundColor: '#bf7326',
          },
          {
            label: 'PQ2A',
            data: this.data.data['2A'],
            backgroundColor: '#cfA336',
          },

          {
            label: 'PQ1D',
            data: this.data.data['1D'],
            backgroundColor: '#A52A2A',
          },
          {
            label: 'PQ1C',
            data: this.data.data['1C'],
            backgroundColor: '#b24a4a',
          },
          {
            label: 'PQ1B',
            data: this.data.data['1B'],
            backgroundColor: '#c06a6a',
          },
          {
            label: 'PQ1A',
            data: this.data.data['1A'],
            backgroundColor: '#ce8a8a',
          },
        ],
      },
      options: {
        plugins: {
          title: {
            //display: false,
            text: '',
            position: 'right',
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
}
