import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { HttpClientModule } from '@angular/common/http';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { NgbModule, NgbModalModule } from '@ng-bootstrap/ng-bootstrap';
import { InlineSVGModule } from 'ng-inline-svg-2';
import { ReportsComponent } from './reports.component';
import { StudentStatisticsComponent } from './student-statistics/student-statistics.component';
import { CourseStatisticsComponent } from './course-statics/course-statics.component';
import { ReportsRoutingModule } from './reports-routing.module';

@NgModule({
  declarations: [
    ReportsComponent,
    StudentStatisticsComponent,
    CourseStatisticsComponent,
  ],
  imports: [
    ReportsRoutingModule,
    CommonModule,
    HttpClientModule,
    FormsModule,
    NgbModule,
    ReactiveFormsModule,
    InlineSVGModule,
    NgbModalModule,
    ReactiveFormsModule,
  ],
})
export class RerportsModule {}
