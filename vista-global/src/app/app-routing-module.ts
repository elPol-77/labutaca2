import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { GlobalComponent } from './components/global/global';

const routes: Routes = [
  // Cuando la ruta está vacía (la raíz del proyecto Angular), carga GlobalComponent
  { path: '', component: GlobalComponent },

  // Si el usuario escribe una ruta rara, mándalo al inicio
  { path: '**', redirectTo: '' }
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }