import { Component } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { BrapciService } from 'src/app/000_core/010_services/brapci.service';

@Component({
  selector: 'app-ai-llm',
  templateUrl: './ai-llm.component.html',
  styleUrls: ['./ai-llm.component.scss']
})
export class AiLlmComponent {
  public id: number = 0;
  public sub: Array<any> | any;
  public full: Array<any> | any;

  constructor(
    private brapciService: BrapciService,
    private route: ActivatedRoute,
    private router: Router,
  ) {}

  ngOnInit() {
    this.sub = this.route.params.subscribe((params) => {
      this.id = +params['id']; // (+) converts string 'id' to a number

      /* Get Full Text */
      this.brapciService
        .api_post('brapci/getText/' + this.id)
        .subscribe((res) => {
          this.full = res;
          console.log(res);
        });
    })
  }
}
