import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { SalesListComponent } from './sale-list/sales-list.component';
import { SalesComponent } from './sales.component';

// const routes: Routes = [{ path: 'list', component: SalesListComponent }];

const routes: Routes = [
  {
    path: '',
    component: SalesComponent,
    children: [
      {
        path: 'list',
        component: SalesListComponent,
      },
    ],
  },
];
@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule],
})
export class SalesRoutingModule {}
