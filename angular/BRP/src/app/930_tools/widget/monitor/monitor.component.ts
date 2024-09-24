import { Component } from '@angular/core';
import { BrapciService } from 'src/app/000_core/010_services/brapci.service';

@Component({
  selector: 'app-monitor',
  templateUrl: './monitor.component.html',
  styleUrls: ['./monitor.component.scss'],
})
export class MonitorComponent {
  result: string = '';
  data: Array<any> | any;

  constructor(private brapciService: BrapciService) {}

  ngOnInit()
    {
      this.brapciService
        .api_post('tools/monitor')
        .subscribe((res) => {
          this.data = res
        });
  }
}
