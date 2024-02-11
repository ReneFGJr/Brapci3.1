import { Component } from '@angular/core';
import { BrapciService } from 'src/app/000_core/010_services/brapci.service';

@Component({
  selector: 'app-event',
  templateUrl: './event.component.html',
})
export class EventComponent {
  public result: Array<any> | any;

  constructor(private brapciService: BrapciService) {}

  ngOnInit() {
    console.log('Events');
    let url = 'event';
    let dt:Array<any> = []
    this.brapciService.api_post(url, dt).subscribe((res) => {
      this.result = res;
      console.log(res)
    });
  }
}
