import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { MainBooksComponent } from './page/main-books.component';
import { VitrineComponent } from './page/vitrine/vitrine.component';
import { MainBookAdminComponent } from './page/admin/main/main.component';
import { BookAddComponent } from './page/admin/book-add/book-add.component';


const routes: Routes = [
  {
    path: '', component: MainBooksComponent, children:
      [
        { path: '', component: VitrineComponent },
        { path: 'admin', component: MainBookAdminComponent },
        { path: 'admin/add', component: BookAddComponent }
      ]
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class MainRoutingModule { }
