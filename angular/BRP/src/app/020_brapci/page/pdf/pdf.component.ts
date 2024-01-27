import { Component, Input } from '@angular/core';
import { BrapciService } from 'src/app/000_core/010_services/brapci.service';

@Component({
  selector: 'app-component-pdf',
  templateUrl: './pdf.component.html'
})
export class PdfComponent {
  @Input() public url:string='';
  @Input() public id: string = '';

  public pdf:string="/assets/icone/pdf.svg"

  constructor(
    public brapciService: BrapciService
  ) {}

  download()
    {
      //window.open(this.url, '_blank', 'location=yes,height=90%,width=90%,scrollbars=yes,status=yes');
      window.open(this.url, '_blank', "width="+screen.availWidth+",height="+screen.availHeight);
    }
}
