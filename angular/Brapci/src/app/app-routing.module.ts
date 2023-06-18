import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { OauthComponent } from './001_auth/oauth/oauth.component';
import { HomeComponent } from './002_main/home/home.component';
import { AboutComponent } from './002_main/home/about/about.component';
import { MainComponent } from './002_main/home/main/main.component';

const APProutes: Routes = [
  { path: 'social', component: OauthComponent},
  { path: 'main', component: HomeComponent,
    children:
    [
      { path: '', component: MainComponent },
      { path: 'about', component: AboutComponent }
    ] }
];

@NgModule({
  imports: [RouterModule.forRoot(APProutes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
