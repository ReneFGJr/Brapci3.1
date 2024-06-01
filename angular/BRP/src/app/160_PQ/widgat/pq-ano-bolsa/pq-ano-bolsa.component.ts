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
  public production: any;
  public chart: Chart | any;

  constructor(public brapciService: BrapciService) {}

  ngOnInit() {
    this.brapciService.api_post('pq/bolsa_ano_tipo').subscribe((res) => {
      this.data = res;
      this.createChart2();
    });
  }


  createChart2() {
    let jsonData = this.data;
    const labels = Object.keys(jsonData);
    let data:Array<any> | any
    console.log(labels);

    const tiposBolsa = new Set<string>();

    labels.forEach((year: string) => {
      Object.keys((jsonData as BolsaData)[year]).forEach((tipo: string) => {
        tiposBolsa.add(tipo);
      });
    });

    console.log(tiposBolsa);

    tiposBolsa.forEach((tipo: string) => {
      const dataset: ChartDataset<'line'> = {
        label: tipo,
        data: labels.map(
          (year: string) => (jsonData as BolsaData)[year][tipo] || 0
        ),
        fill: false,
        borderColor: this.getRandomColor(),
        tension: 0.1,
      };
      console.log(data);
      data.datasets.push(dataset);
    });

    this.chart = new Chart('myChart', {
      type: 'line',
      data: data,
      options: {
        responsive: true,
        plugins: {
          legend: {
            position: 'top',
          },
          title: {
            display: true,
            text: 'Modalidades de Bolsa por Ano',
          },
        },
      },
    });
  }

  getRandomColor(): string {
    const letters = '0123456789ABCDEF';
    let color = '#';
    for (let i = 0; i < 6; i++) {
      color += letters[Math.floor(Math.random() * 16)];
    }
    return color;
  }
}
