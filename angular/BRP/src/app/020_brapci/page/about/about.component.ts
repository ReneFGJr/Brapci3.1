import { Component } from '@angular/core';
import { BrapciService } from '../../../000_core/010_services/brapci.service';

@Component({
  selector: 'app-about',
  templateUrl: './about.component.html',
  styleUrls: ['./about.component.scss']
})
export class AboutComponent {
  public resume:Array<any>|any

  constructor(
    private brapciService:BrapciService
  ) {}

  ngOnInit(): void {
    this.brapciService.generic('resume').subscribe(
      res=>{
        this.resume = res
        console.log(res);
      }
    )
    //Called after the constructor, initializing input properties, and the first call to ngOnChanges.
    //Add 'implements OnInit' to the class.
  }
}
