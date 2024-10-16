// task-classes.component.ts
import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { CourseService } from '../service/course.service';

@Component({
  selector: 'app-task-classes',
  templateUrl: './task-classes.component.html',
  // styleUrls: ['./task-classes.component.scss']
})
export class TaskClassesComponent implements OnInit {
  classes: any[] = [];
  isLoading: any;
  courseId: number;

  constructor(
    private courseService: CourseService,
    private route: ActivatedRoute
  ) {}

  ngOnInit(): void {
    this.isLoading = this.courseService.isLoading$;
    this.courseId = +(this.route.snapshot.paramMap.get('id') ?? 0);
    this.loadClasses();
  }

  loadClasses() {
    this.courseService
      .getCourseClasses(this.courseId)
      .subscribe((resp: any) => {
        this.classes = resp;
      });
  }
}
