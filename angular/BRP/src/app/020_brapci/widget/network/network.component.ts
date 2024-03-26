import { DataVComponent } from './../../page/v/data/data.component';
import { Component, Input } from '@angular/core';
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
  @Input() public data: Array<any> | any;
  dataL: Array<any> | any;
  dataN: Array<any> | any;
  Highcharts: typeof Highcharts = Highcharts
  chartOptions: Highcharts.Options | any


  ngOnInit(): void {
    this.dataL = this.data.network.data,
    this.dataN = this.data.network.nodes,
    this.chartOptions = {
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
          data: this.dataL,
          /*[
            { from: 'Node 1', to: 'Node 2' },
            { from: 'Node 1', to: 'Node 3', color: '#888', width: 2, dashStyle: 'dot' }, // Aresta mais grossa
            { from: 'Node 2', to: 'Node 4' },
            // Adicione mais dados aqui
          ],
          */
          nodes: this.dataN
            /*
            { id: 'Node 1', marker: { radius: 20, color: '#f00' } }, // Tamanho específico para 'Node 1'
            { id: 'Node 2', color: '#f00', marker: { radius: 10 } }, // Tamanho específico para 'Node 1'
            */
          ,
          lineWidth: 5,
        },
      ],
    };
  }

  constructor() {}
}
