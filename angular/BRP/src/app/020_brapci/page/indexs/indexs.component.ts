import { Component } from '@angular/core';
import { BrapciService } from 'src/app/000_core/010_services/brapci.service';

@Component({
  selector: 'app-indexs',
  templateUrl: './indexs.component.html',
  styleUrls: ['./indexs.component.scss'],
})
export class IndexsComponent {
  public header: string = 'Índices';
  public type = 'subject';
  public data: Array<any> | any;

  constructor(public brapciService: BrapciService) {}

  ngOnInit() {
    console.log(this.type);
    this.brapciService.generic('indexs/'+this.type).subscribe((res) => {
      this.data = res;
    });
  }
}
