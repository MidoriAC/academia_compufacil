import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { RoleManagementComponent } from './role.component';
import { RoleListComponent } from './role-list/role-list.component';

const routes: Routes = [
  {
    path: '',
    component: RoleManagementComponent,
    children: [
      {
        path: 'list',
        component: RoleListComponent,
      },
    ],
  },
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule],
})
export class RoleRoutingModule {}
