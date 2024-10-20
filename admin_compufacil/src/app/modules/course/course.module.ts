import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { CourseRoutingModule } from './course-routing.module';
import { CourseComponent } from './course.component';
import { CourseAddComponent } from './course-add/course-add.component';
import { CourseEditComponent } from './course-edit/course-edit.component';
import { CourseDeleteComponent } from './course-delete/course-delete.component';
import { CourseLitsComponent } from './course-lits/course-lits.component';
import { HttpClientModule } from '@angular/common/http';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { NgbModule, NgbModalModule } from '@ng-bootstrap/ng-bootstrap';
import { InlineSVGModule } from 'ng-inline-svg-2';
import { CKEditorModule } from 'ckeditor4-angular';
import { SectionAddComponent } from './section/section-add/section-add.component';
import { SectionEditComponent } from './section/section-edit/section-edit.component';
import { SectionDeleteComponent } from './section/section-delete/section-delete.component';
import { ClaseAddComponent } from './section/clases/clase-add/clase-add.component';
import { ClaseEditComponent } from './section/clases/clase-edit/clase-edit.component';
import { ClaseDeleteComponent } from './section/clases/clase-delete/clase-delete.component';
import { ClaseFileDeleteComponent } from './section/clases/clase-file-delete/clase-file-delete.component';
import { TaskCoursesComponent } from './task-courses/task-courses.component';
import { TaskClassesComponent } from './task-classes/task-classes.component';
import { TaskDetailComponent } from './task-detail/task-detail.component';

@NgModule({
  declarations: [
    CourseComponent,
    CourseAddComponent,
    CourseEditComponent,
    CourseDeleteComponent,
    CourseLitsComponent,
    SectionAddComponent,
    SectionEditComponent,
    SectionDeleteComponent,
    ClaseAddComponent,
    ClaseEditComponent,
    ClaseDeleteComponent,
    ClaseFileDeleteComponent,
    TaskCoursesComponent,
    TaskClassesComponent,
    TaskDetailComponent,
  ],
  imports: [
    CommonModule,
    CourseRoutingModule,

    HttpClientModule,
    FormsModule,
    NgbModule,
    ReactiveFormsModule,
    InlineSVGModule,
    NgbModalModule,

    CKEditorModule,
  ],
})
export class CourseModule {}
