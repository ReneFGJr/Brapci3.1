import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { KanbanRoutingModule } from './kanban-routing.module';
import { PostitComponent } from './gadget/postit/postit.component';
import { TasksComponent } from './page/tasks/tasks.component';
import { KanBanMainComponent } from './page/main/main.component';

import { DragDropModule } from '@angular/cdk/drag-drop';


@NgModule({
  declarations: [KanBanMainComponent, PostitComponent, TasksComponent],
  imports: [CommonModule, KanbanRoutingModule, DragDropModule],
})
export class KanbanModule {}
