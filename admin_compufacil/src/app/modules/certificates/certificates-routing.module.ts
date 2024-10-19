import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { CertificateComponent } from './certificates.component';
import { StudentCertificatesComponent } from './certificates-list/certificates.component';

// const routes: Routes = [{ path: 'list', component: SalesListComponent }];

const routes: Routes = [
  {
    path: '',
    component: CertificateComponent,
    children: [
      {
        path: 'list',
        component: StudentCertificatesComponent,
      },
    ],
  },
];
@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule],
})
export class CertificateRoutingModule {}
