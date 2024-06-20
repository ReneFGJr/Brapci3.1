import { Component } from '@angular/core';
import Highcharts from 'highcharts';

@Component({
  selector: 'app-painel-years',
  templateUrl: './years.component.html',
  styleUrls: ['./years.component.scss'],
})
export class YearsPainelComponent {
  Highcharts: typeof Highcharts = Highcharts;
  chartOptions: Highcharts.Options = {
    title: {
      text: 'Produção Quantitativa por Ano',
    },
    xAxis: {
      categories: this.getYearsArray(1962, 2024),
    },
    yAxis: {
      title: {
        text: 'Quantidade Produzida',
      },
    },
    series: [
      {
        name: 'Produção',
        type: 'line',
        data: this.getProductionData(1962, 2024),
      },
    ],
  };

  getYearsArray(startYear: number, endYear: number): string[] {
    const years = [];
    for (let year = startYear; year <= endYear; year++) {
      years.push(year.toString());
    }
    return years;
  }

  getProductionData(startYear: number, endYear: number): number[] {
    const data = [];
    for (let year = startYear; year <= endYear; year++) {
      data.push(Math.floor(Math.random() * 1000)); // Substitua esta linha com seus dados reais
    }
    return data;
  }
}
