import { Component } from '@angular/core';
import { Router } from '@angular/router';
import { BrapciService } from 'src/app/service/brapci.service';

@Component({
  selector: 'app-evento',
  templateUrl: './evento.component.html',
  styleUrls: ['./evento.component.scss']
})
export class EventoComponent {

  constructor(
    private brapciService:BrapciService,
    private router: Router
  ) {}

public events: Array<any> | any;

assign(id:string)
{
  alert(id)
  this.router.navigate(['inscricao/'+id]);
}

ngOnInit()
  {
    this.brapciService.events().subscribe(
      res=>{          
          this.events = res
      }
    )
  }
}
