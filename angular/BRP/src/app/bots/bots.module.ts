import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { BotsRoutingModule } from './bots-routing.module';
import { BotsWelcomeComponent } from './welcome/welcome.component';
import { ProcessComponent } from './page/process/process.component';
import { RobotiComponent } from './widgat/roboti/roboti.component';
import { ArticleComponent } from './page/article/article.component';
import { PersonComponent } from './page/person/person.component';
import { PersonSearchComponent } from './widget/person-search/person-search.component';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatAutocompleteModule } from '@angular/material/autocomplete';
import { ReactiveFormsModule } from '@angular/forms';


@NgModule({
  declarations: [
    BotsWelcomeComponent,
    ProcessComponent,
    RobotiComponent,
    ArticleComponent,
    PersonComponent,
    PersonSearchComponent,
  ],
  imports: [
    CommonModule,
    BotsRoutingModule,
    MatFormFieldModule,
    MatInputModule,
    MatAutocompleteModule,
    ReactiveFormsModule,
  ],
})
export class BotsModule {}
