import { Component } from '@angular/core';

// Variable $ Pertenece a Jquery
declare var $: any;

// se declara la función proveniente deñ assets/js/main.js
declare function HOMEINIT([]): any;
@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css'],
})
export class AppComponent {
  title = 'courses_onlines';
  constructor() {
    setTimeout(() => {
      HOMEINIT($);
    }, 50);
  }
}
