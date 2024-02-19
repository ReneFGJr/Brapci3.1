import { Component } from '@angular/core';
import { BrapciService } from 'src/app/000_core/010_services/brapci.service';

@Component({
  selector: 'app-kanban-main',
  templateUrl: './main.component.html',
  styleUrls: ['./main.component.scss'],
})
export class KanBanMainComponent {
  constructor(private brapciService: BrapciService) {}
  public user = 'RENE';
  public tasks: Array<any> | any;

  ngOnInit() {
    let dt: Array<any> | any = { user: '1' };

    this.brapciService.api_post('kanban', dt).subscribe(
      (res) => {
        this.tasks = res;
      },
      (error) => error
    );
  }
}
