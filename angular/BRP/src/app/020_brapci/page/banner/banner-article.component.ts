import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-banner-article',
  templateUrl: './banner-article.component.html',
  styleUrls: ['./banner.component.scss']
})
export class BannerArticleComponent {
  @Input() public section:Array<any> | any;
  @Input() public publisher:string = '';
  @Input() public cover: string = '';
  ngOnInit()
    {
      console.log("==============xxxxxxxxxxxxxxx==============")
      console.log(this.section);
    }
}
