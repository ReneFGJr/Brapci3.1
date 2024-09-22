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

  public trabalhos: Array<any> | any;

  constructor(private brapciService: BrapciService) {}

  ngOnInit(): void {
    this.brapciService
      .api_post('indicator/ProducaoJournalAno/' + this.jid)
      .subscribe((res) => {
        console.log('====================XX==');
        this.data = res;
        this.trabalhos = this.data.trabalhos;
        console.log(this.trabalhos)

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

        this.createBAR();
      });
  }

  public Highcharts: typeof Highcharts = Highcharts; // Reference Highcharts library
  public chartOptions: Highcharts.Options | any;

  createBAR() {
    this.chartOptions = {
      chart: {
        type: 'column',
      },
      title: {
        text: 'Ano de produção',
      },
      xAxis: {
        type: 'value',
        categories: this.categories,
      },
      yAxis: {
        title: {
          text: 'Trabalhos publicados',
        },
      },
      time: {
        useUTC: false, // Defina se necessário
      },
      series: [
        {
          name: 'Publicação anual',
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
}
