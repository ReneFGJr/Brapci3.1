import { Component } from '@angular/core';
import { BrapciService } from '../../../000_core/010_services/brapci.service';

@Component({
  selector: 'app-about',
  templateUrl: './about.component.html'
})
export class AboutComponent {
  public content:Array<any>|any
  public header = {'title':'Sobre a Brapci'}

  constructor(
    private brapciService:BrapciService
  ) {}

  ngOnInit(): void {
    this.brapciService.api_post('page/about').subscribe(
      res=>{
        this.content = res;
        console.log(res);
      }
    )
    //Called after the constructor, initializing input properties, and the first call to ngOnChanges.
    //Add 'implements OnInit' to the class.
  }
}
