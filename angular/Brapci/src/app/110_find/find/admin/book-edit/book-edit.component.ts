import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { UIbooksItems } from 'src/app/100_brapci_livros/interface/UIbooksItems';
import { VitrineLivrosService } from 'src/app/100_brapci_livros/service/vitrine-livros.service';

@Component({
  selector: 'app-book-edit',
  templateUrl: './book-edit.component.html',
  styleUrls: ['./book-edit.component.css']
})
export class BookEditComponent {
  constructor(
    private vitrineLivrosService: VitrineLivrosService,
    private route: ActivatedRoute
  ) { }

  public id:number = 0
  public PreparoItems: Array<UIbooksItems> | any;

  public editMode:boolean = true;
  public undo: string = '';
  public edit_be_title:boolean = false;

  ngOnInit()
    {
      this.id = Number(this.route.snapshot.paramMap.get('id'))
      console.log(this.id)

      this.vitrineLivrosService.getItem(this.id).subscribe(
        res => {
          this.PreparoItems = res;
        },
        error => error
      );
    }

    save(block:string)
      {
          this.vitrineLivrosService.save(this.id, block, this.PreparoItems[block]).subscribe(
          res => {
            this.PreparoItems = res;
            this.editOFF(block);
          },
          error => error
        );
      }

    editON(block:string)
      {
        this.editMode = false;
      if (block == 'be_title') { this.edit_be_title = true; this.undo = String(this.PreparoItems.be_title); }
      }

    editOFF(block: string) {
      this.editMode = true;
      if (block == 'be_title') { this.edit_be_title = false; }
    }

    cancel(block: string) {
      if (block == 'be_title') { this.edit_be_title = false; this.editOFF(block); this.PreparoItems.be_title = this.undo; }
    }
}
