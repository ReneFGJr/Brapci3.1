import { Component } from '@angular/core';
import * as Highcharts from 'highcharts';
import Networkgraph from 'highcharts/modules/networkgraph';

// Inicializa o módulo networkgraph
Networkgraph(Highcharts);

@Component({
  selector: 'app-network',
  templateUrl: './network.component.html',
  styleUrls: ['./network.component.scss'],
})
export class NetworkComponent {
  Highcharts: typeof Highcharts = Highcharts; // necessário para acessar o Highcharts na template
  chartOptions: Highcharts.Options = {
    chart: {
      type: 'networkgraph',
      plotBorderWidth: 1,
      backgroundColor: 'transparent',
    },
    title: {
      text: '',
    },

    series: [
      {
        type: 'networkgraph',
        dataLabels: {
          enabled: true,
          linkFormat: '',
        },
        layoutAlgorithm: {
          enableSimulation: true,
          //friction: -0.9,
        },
        marker: {
          radius: 10, // Define um tamanho padrão para os nós
        },
        data: [
          { from: 'Node 1', to: 'Node 2' },
          { from: 'Node 1', to: 'Node 3', color: '#888', width: 2, dashStyle: 'dot' }, // Aresta mais grossa
          { from: 'Node 2', to: 'Node 4' },
          // Adicione mais dados aqui
        ],
        nodes: [
          { id: 'Node 1', marker: { radius: 20, color: '#f00' } }, // Tamanho específico para 'Node 1'
          { id: 'Node 2', color: '#f00', marker: { radius: 10 } }, // Tamanho específico para 'Node 1'
        ],
        lineWidth: 5,
      },
    ],
  };

  constructor() {}

  ngOnInit() {}
}
