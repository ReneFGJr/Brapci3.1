import { Component } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { BrapciService } from 'src/app/000_core/010_services/brapci.service';

@Component({
  selector: 'app-indexs',
  templateUrl: './indexs.component.html',
  styleUrls: ['./indexs.component.scss'],
})
export class IndexsComponent {
  public header: string = 'Índices';
  public type = '';
  public data: Array<any> | any;
  public ltr: string = '';
  public sub: Array<any> | any;

  constructor(
    public brapciService: BrapciService,
    private route: ActivatedRoute
  ) {}

  ngOnChange()
    {
      console.log("NEW")
    }

  ngOnInit() {
    this.sub = this.route.params.subscribe((params) => {
      //this.id = +params['id']; // (+) converts string 'id' to a number
      this.ltr = params['id']; // (+) converts string 'id' to a number
      this.type = params['type']; // (+) converts string 'id' to a number
    });

    console.log(this.type);
    this.brapciService
      .generic('indexs/' + this.type + '/' + this.ltr)
      .subscribe((res) => {
        this.data = res;
      });
  }
}
