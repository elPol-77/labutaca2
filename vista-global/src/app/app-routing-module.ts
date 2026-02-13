import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { GlobalComponent } from './components/global/global';

const routes: Routes = [
  { path: '', component: GlobalComponent },

  { path: '**', redirectTo: '' }
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }