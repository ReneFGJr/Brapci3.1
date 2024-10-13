import { Component, Input } from '@angular/core';
import { BrapciService } from 'src/app/000_core/010_services/brapci.service';

@Component({
  selector: 'app-processing-workflow',
  templateUrl: './processing-workflow.component.html',
  styleUrls: ['./processing-workflow.component.scss'],
})
export class ProcessingWorkflowComponent {
  @Input() public toolsName: string = 'TXT para .NET';
  @Input() public toolsNameDescription: string = '';
  @Input() public buttonName: string = 'Converter para .NET';
  @Input() public endpoint: string = '';

  constructor(private brapciService: BrapciService) {}

  public data: Array<any> | any;
  public dataset: Array<any> | any;
  public datasetR: Array<any> | any;
  public status = 0;

  // Método que recebe o dataset atualizado do componente filho
  updateDataset(newDataset: Array<any>) {
    this.dataset = newDataset; // Atualiza o dataset no pai
    this.status = 1;
  }

  restart() {
    this.status = 0;
    this.dataset = null;
  }

  process() {
    this.datasetR = this.dataset;
    this.status = 2;
    console.log(this.endpoint);

    this.brapciService.api_post(this.endpoint, this.dataset).subscribe(
      (res) => {
        this.data = res;
        console.log(this.data);
        this.status = 3;
      },
      (error) => error
    );
  }

  processed() {
    this.status = 3;
  }
}
