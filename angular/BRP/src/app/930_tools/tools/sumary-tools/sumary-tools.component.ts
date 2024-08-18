import { Component } from '@angular/core';
import { FormBuilder, FormGroup } from '@angular/forms';
import { ActivatedRoute } from '@angular/router';
import { BrapciService } from 'src/app/000_core/010_services/brapci.service';

@Component({
  selector: 'app-sumary-tools',
  templateUrl: './sumary-tools.component.html',
})
export class SumaryToolsComponent {
  constructor(
    private fb: FormBuilder,
    private brapciService: BrapciService,
    private route: ActivatedRoute
  ) {
    this.summaryForm = this.fb.group({
      summaryText: [''],
    });
  }

  summaryForm: FormGroup;
  processedSummary: Array<any> | any;
  sub: Array<any> | any;
  book: Array<any> | any;
  id: string = '';

  ngOnInit() {
    this.sub = this.route.params.subscribe((params) => {
      this.id = params['id']; // (+) converts string 'id' to a number
      this.brapciService.api_post('get/v/'+this.id).subscribe((res) => {
        console.log(res);
        this.book = res;
      });
    });
  }

  onSubmit() {
    let dt: Array<any> | any = {
      text: this.summaryForm.value.summaryText,
      action: 'Summarize',
    };
    this.brapciService.api_post('tools/mark', dt).subscribe((res) => {
      console.log(res);
      this.processedSummary = res;
    });
  }
}
