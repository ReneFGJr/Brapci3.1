import { Component, Input } from '@angular/core';
import { BrapciService } from 'src/app/020_brapci/service/brapci.service';

@Component({
  selector: 'app-component-pdf',
  templateUrl: './pdf.component.html',
  styleUrls: ['./action.component.scss']
})
export class PdfComponent {
  @Input() public url:string='';
  @Input() public id: string = '';

  constructor(
    public brapciService: BrapciService
  ) {}

  download()
    {
      alert("download " +this.id);
      alert("download " + this.url);
      window.open(this.url, '_blank', 'location=yes,height=570,width=520,scrollbars=yes,status=yes');
    }
}
