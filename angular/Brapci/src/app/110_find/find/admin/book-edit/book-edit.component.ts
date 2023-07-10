import { Component } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { UIauthors } from 'src/app/100_brapci_livros/interface/UIauthors';
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
  public listAuthors:Array<UIauthors> | any;
  public isbn: string='';

  public editMode:boolean = true;
  public undo: string = '';
  public edit_bk_title:boolean = false;
  public edit_be_year: boolean = false;
  public edit_be_cover: boolean = false;
  public edit_be_authors: boolean = false;

  ngOnInit()
    {
      this.id = Number(this.route.snapshot.paramMap.get('id'))

      this.vitrineLivrosService.getISBN(this.id).subscribe(
        res => {
          this.PreparoItems = res;
          this.isbn = this.PreparoItems.isbn13;
          this.listAuthors = this.PreparoItems.authors;
          console.log(this.PreparoItems);
        },
        error => error
      );
    }

    save(block:string)
      {
        alert('save - '+block);
          this.vitrineLivrosService.save(this.id, block, this.PreparoItems[block]).subscribe(
          res => {
            this.PreparoItems = res;
            console.log(this.PreparoItems);
            this.editOFF(block);
          },
          error => error
        );
      }

    editON(block:string)
      {
      this.editMode = false;
      if (block == 'bk_title') { this.edit_bk_title = true; this.undo = String(this.PreparoItems.bk_title); }
      if (block == 'be_year') { this.edit_be_year = true; this.undo = String(this.PreparoItems.be_year); }
      if (block == 'be_cover') { this.edit_be_cover = true; this.undo = String(this.PreparoItems.be_cover); }
      if (block == 'be_authors') { this.edit_be_authors = true; this.undo = String(this.PreparoItems.be_authors); }
      }

    editOFF(block: string) {
      this.editMode = true;
      if (block == 'bk_title') { this.edit_bk_title = false; }
      if (block == 'be_year') { this.edit_be_year = false; }
      if (block == 'be_cover') { this.edit_be_cover = false; }
      if (block == 'be_authors') { this.edit_be_authors = false; }
    }

    cancel(block: string) {
      if (block == 'bk_title') { this.edit_bk_title = false; this.editOFF(block); this.PreparoItems.be_title = this.undo; }
      if (block == 'be_year') { this.edit_be_year = false; this.editOFF(block); this.PreparoItems.be_year = this.undo; }
      if (block == 'be_cover') { this.edit_be_cover = false; this.editOFF(block); this.PreparoItems.be_cover = this.undo; }
      if (block == 'be_authors') { this.edit_be_authors = false; this.editOFF(block); this.PreparoItems.be_authors = this.undo; }
    }
}
