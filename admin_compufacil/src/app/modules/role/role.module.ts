import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { HttpClientModule } from '@angular/common/http';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { NgbModule, NgbModalModule } from '@ng-bootstrap/ng-bootstrap';
import { InlineSVGModule } from 'ng-inline-svg-2';
import { RoleManagementComponent } from './role.component';
import { RoleListComponent } from './role-list/role-list.component';
import { RoleRoutingModule } from './role-routing.module';
import { RoleModalComponent } from './role-list/role-modal.component';

@NgModule({
  declarations: [
    RoleManagementComponent,
    RoleListComponent,
    RoleModalComponent,
  ],
  imports: [
    CommonModule,
    RoleRoutingModule,
    HttpClientModule,
    FormsModule,
    NgbModule,
    ReactiveFormsModule,
    InlineSVGModule,
    NgbModalModule,
    ReactiveFormsModule,
  ],
})
export class RoleModule {}
