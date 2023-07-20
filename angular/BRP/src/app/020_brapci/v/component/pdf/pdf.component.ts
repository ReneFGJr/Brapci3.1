import { Component, Input } from '@angular/core';
import { BrapciService } from 'src/app/020_brapci/service/brapci.service';

@Component({
  selector: 'app-component-pdf',
  templateUrl: './pdf.component.html',
  styleUrls: ['./action.component.scss']
})
export class PdfComponent {
  @Input() public url:string='';

  constructor(
    public brapciService: BrapciService
  ) {}
}
