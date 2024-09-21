import { Component, Input, OnInit } from '@angular/core';
import Highcharts from 'highcharts';
import { BrapciService } from 'src/app/000_core/010_services/brapci.service';

@Component({
  selector: 'app-icr-producao-ano',
  templateUrl: './icr-producao-ano.component.html',
})
export class IcrProducaoAnoComponent {
  @Input() public jid: string = '';

  public data: Array<any> | any;

  public categories: Array<any> | any;
  public seriesData: Array<any> | any;

  constructor(private brapciService: BrapciService) {}

  ngOnInit(): void {
    this.brapciService
      .api_post('indicator/ProducaoJournalAno/' + this.jid)
      .subscribe((res) => {
        this.data = res;
        this.data = this.data.data;
        //this.loadChart();

        if (Array.isArray(this.data)) {
          this.categories = this.data.map((item) => item.year);
        } else {
          console.error('this.data não é um array:', this.data);
        }

        if (Array.isArray(this.data)) {
          //this.seriesData = this.data.map((item) => parseInt((item) => (item.total, 10))
          //this.seriesData = this.data.map((item) => parseInt(item) => item.total);
          this.seriesData = this.data.map((item) => parseInt(item.total));
        } else {
          console.error('this.data não é um array:', this.data);
        }

        this.createBAR()
      });
  }

  public Highcharts: typeof Highcharts = Highcharts; // Reference Highcharts library
  public chartOptions: Highcharts.Options | any

  createBAR()
  {
    this.chartOptions = {
      chart: {
        type: 'column',
      },
      title: {
        text: 'Produção de trabalhos por ano',
      },
      xAxis: {
        categories: this.categories,
      },
      yAxis: {
        title: {
          text: 'Valor',
        },
      },
      series: [
        {
          name: 'Dados do Ano',
          data: this.seriesData,
          type: 'column',
          dataLabels: {
            enabled: true, // Habilita os valores nas barras
            format: '{y}', // Mostra o valor do dado
          },
        },
      ],
    };
  }

  /*

  loadChart(): void {
    // Verifique se 'this.data' é um array antes de chamar '.map()'

    /*



    */

  /*
    const categories = this.jsonData.data.map((item) => item.year);
    const seriesData = this.jsonData.data.map((item) =>
      parseInt(item.total, 10)
    );

    console.log(categories);
    console.log(seriesData);

    this.chartOptions = {
      chart: {
        type: 'column',
      },
      title: {
        text: 'Produção científica da publicação por ano',
        align: 'left',
      },
      subtitle: {
        text:
          'Source: <a target="_blank" ' +
          'href="https://brapci.inf.br/">Brapci</a>',
        align: 'left',
      },
      xAxis: {
        categories: ['USA', 'China', 'Brazil', 'EU', 'Argentina', 'India'],
        crosshair: true,
        accessibility: {
          description: 'Countries',
        },
      },
      yAxis: {
        min: 0,
        title: {
          text: '1000 metric tons (MT)',
        },
      },
      tooltip: {
        valueSuffix: ' (1000 MT)',
      },
      plotOptions: {
        column: {
          pointPadding: 0.2,
          borderWidth: 0,
        },
      },
      series: [
        {
          name: 'Corn',
          data: [387749, 280000, 129000, 64300, 54000, 34300],
          type: 'number',
        },
      ],
    };

  }
  */
}
