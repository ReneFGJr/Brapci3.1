import { DataVComponent } from './../../page/v/data/data.component';
import { Component, Input, ViewChild } from '@angular/core';
import * as Highcharts from 'highcharts';
import { HighchartsChartComponent } from 'highcharts-angular';
import Networkgraph from 'highcharts/modules/networkgraph';

// Inicializa o módulo networkgraph
Networkgraph(Highcharts);



@Component({
  selector: 'app-network',
  templateUrl: './network.component.html',
  styleUrls: ['./network.component.scss'],
})
export class NetworkComponent {
  @ViewChild(HighchartsChartComponent, { static: false })
  public chartComponent: HighchartsChartComponent | any;

  @Input() public data: Array<any> | any;
  dataL: Array<any> | any;
  dataN: Array<any> | any;
  Highcharts: typeof Highcharts = Highcharts;
  chartOptions: Highcharts.Options | any;

  ngOnInit(): void {
    this.dataL = this.data.network.data;
    this.dataN = this.data.network.nodes;
    this.createGraf();
  }

  ngOnChanges(): void {
    console.log('CHANGE');

    const chart = this.chartComponent.chart; // Acesso à instância do gráfico Highcharts
    // Agora você pode usar os métodos do Highcharts, como `series[0].setData(newData)`
    console.log(chart.series);
    chart.series[0].setData(this.data.network.data);
    chart.series[0].nodes = this.data.network.data;

    chart.redraw();
  }

  updateNodesAndLinks(newNodes:any, newLinks:any): void {
    // Supondo que `chartOptions` seja a propriedade vinculada ao <highcharts-chart>
    this.chartOptions.series[0].data = this.data.network.data;
    this.chartOptions.series[0].nodes = this.data.network.data;

    // Atualizar a referência para que o Angular detecte a mudança
    this.chartOptions = { ...this.chartOptions };
  }

  createGraf(): void {
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
            friction: -0.9,
            integration: 'verlet',
            function(d: number) {
              return Math.min(d * d * 0.01, 200); // Ajuste esse valor conforme necessário
            },
          },
          marker: {
            radius: 10, // Define um tamanho padrão para os nós
          },
          data: this.dataL,
          nodes: this.dataN,
          lineWidth: 5,
        },
      ],
    };
  }

  constructor() {}
}
