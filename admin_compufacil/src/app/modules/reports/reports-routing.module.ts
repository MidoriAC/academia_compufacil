import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { ReportsComponent } from './reports.component';
import { CourseStatisticsComponent } from './course-statics/course-statics.component';
import { StudentStatisticsComponent } from './student-statistics/student-statistics.component';

const routes: Routes = [
  {
    path: '',
    component: ReportsComponent,
    children: [
      {
        path: 'student',
        component: StudentStatisticsComponent,
      },
      {
        path: 'course',
        component: CourseStatisticsComponent,
      },
    ],
  },
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule],
})
export class ReportsRoutingModule {}
