import { Component } from '@angular/core';
import { BrapciService } from 'src/app/000_core/010_services/brapci.service';

@Component({
  selector: 'app-brapci-news',
  templateUrl: './news.component.html',
})
export class NewsComponent {
  label_news: string = 'Novidades';
  text: string = ''
  result: Array<any> | any = []

  constructor(
    private brapciService: BrapciService
  ) {}

  ngOnInit() {
      let dt:Array<any> | any = {}
      let url = 'brapci/news';
      this.brapciService.api_post(url,dt).subscribe((res) => {
        console.log("============S")
        console.log(res)
        console.log('============F');
        this.result = res
      });
  }
}
