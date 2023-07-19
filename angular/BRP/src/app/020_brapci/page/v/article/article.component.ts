import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-brapci-article',
  templateUrl: './article.component.html',
  styleUrls: ['./article.component.scss']
})
export class ArticleComponent {
  @Input() public data:Array<any> | any

  ngOnInit(): void {
    console.log("=============")
    console.log(this.data);
    console.log("=============")
    //Called after the constructor, initializing input properties, and the first call to ngOnChanges.
    //Add 'implements OnInit' to the class.

  }

}
