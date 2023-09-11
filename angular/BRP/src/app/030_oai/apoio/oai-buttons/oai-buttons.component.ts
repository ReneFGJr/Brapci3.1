import { Component, Input } from '@angular/core';
import { BrapciService } from 'src/app/000_core/010_services/brapci.service';

@Component({
  selector: 'app-oai-buttons',
  templateUrl: './oai-buttons.component.html',
  styleUrls: ['./oai-buttons.component.scss'],
})
export class OaiButtonsComponent {
  @Input() public sources: Array<any> | any;
  public size: number = 100;
  public oai: Array<any> | any;

  constructor(private brapciService: BrapciService) {}

  processOAI(issue:number, sta: number) {
    console.log(sta);
    console.log(issue);
    this.brapciService.harvestingIssue(issue).subscribe((res) => {
      this.oai = res;
    });
  }

  onSize() {
    this.size = this.size + 1;
  }
}
