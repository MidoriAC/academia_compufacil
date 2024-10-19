import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { HttpClientModule } from '@angular/common/http';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { NgbModule, NgbModalModule } from '@ng-bootstrap/ng-bootstrap';
import { InlineSVGModule } from 'ng-inline-svg-2';
import { CKEditorModule } from 'ckeditor4-angular';
import { CertificateComponent } from './certificates.component';
import { StudentCertificatesComponent } from './certificates-list/certificates.component';
import { CertificateRoutingModule } from './certificates-routing.module';

@NgModule({
  declarations: [CertificateComponent, StudentCertificatesComponent],
  imports: [
    CommonModule,
    CertificateRoutingModule,
    HttpClientModule,
    FormsModule,
    NgbModule,
    ReactiveFormsModule,
    InlineSVGModule,
    NgbModalModule,

    CKEditorModule,
  ],
})
export class CertificateModule {}
