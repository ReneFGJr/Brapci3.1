import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-brapci-proceeding',
  templateUrl: './proceeding.component.html',
  styleUrls: ['./proceeding.component.scss'],
})
export class BrapciProceedingComponent {
  @Input() public data: Array<any> | any;
  public url: string = '';
  public rdf: string = '/assets/img/icone_rdf.png';
  public header: Array<any> | any = null;

  ngOnInit(): void {
    this.header = [];
    this.header = { title: 'Anais de Evento' };

    console.log('===================');
    console.log(this.data.cover);
    //Called after the constructor, initializing input properties, and the first call to ngOnChanges.
    //Add 'implements OnInit' to the class.  }
  }
}
