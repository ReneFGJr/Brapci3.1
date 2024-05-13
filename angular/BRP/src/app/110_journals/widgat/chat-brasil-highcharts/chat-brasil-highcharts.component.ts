import { Component } from '@angular/core';
import * as Highcharts from 'highcharts';

@Component({
  selector: 'app-chat-brasil-highcharts',
  templateUrl: './chat-brasil-highcharts.component.html',
})
export class ChatBrasilHighchartsComponent {
  ngOnInit() {
    console.log('HELLO-MAP');
  }

  Highcharts: typeof Highcharts = Highcharts; // passa o Highcharts como uma propriedade para o HTML
  chartOptions: Highcharts.Options = {
    chart: {
      type: 'column',
    },
    title: {
      text: 'Distribuição por Estados',
    },
    xAxis: {
      categories: ['SP', 'RJ', 'MG', 'ES', 'Outros'],
      title: {
        text: 'Estados',
      },
    },
    yAxis: {
      min: 0,
      title: {
        text: 'População (milhões)',
      },
    },
    series: [
      {
        name: 'População',
        data: [12.9, 6.7, 10.1, 1.8, 14.5],
        type: 'column',
      },
    ],
  };
}
