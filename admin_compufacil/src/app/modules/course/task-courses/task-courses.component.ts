import { Component, OnInit } from '@angular/core';
import { CourseService } from '../service/course.service';

@Component({
  selector: 'app-task-courses',
  templateUrl: './task-courses.component.html',
  // styleUrls: ['./task-courses.component.scss']
})
export class TaskCoursesComponent implements OnInit {
  courses: any[] = [];
  isLoading: any;

  constructor(private courseService: CourseService) {}

  ngOnInit(): void {
    this.isLoading = this.courseService.isLoading$;
    this.loadCourses();
  }

  loadCourses() {
    this.courseService.getTaskCourses().subscribe((resp: any) => {
      this.courses = resp;
    });
  }
}
