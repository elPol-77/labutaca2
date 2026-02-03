import { NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';
import { HttpClientModule } from '@angular/common/http';
import { FormsModule } from '@angular/forms'; 
import { CommonModule } from '@angular/common';
import { AppRoutingModule } from './app-routing-module';
import { App } from './app';
import { GlobalComponent } from './components/global/global'; 

@NgModule({
  declarations: [
    App,
  ],
  imports: [
    BrowserModule,
    AppRoutingModule,
    HttpClientModule, 
    FormsModule,
    GlobalComponent,
    CommonModule,
  ],
  providers: [],
  bootstrap: [App]
})
export class AppModule { }