// task-detail.component.ts
import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { CourseService } from '../service/course.service';

import { Modal } from 'bootstrap';

@Component({
  selector: 'app-task-detail',
  templateUrl: './task-detail.component.html',
})
export class TaskDetailComponent implements OnInit {
  studentTasks: any[] = [];
  isLoading: any;
  taskId: number;

  constructor(
    private courseService: CourseService,
    private route: ActivatedRoute
  ) {}

  ngOnInit(): void {
    this.isLoading = this.courseService.isLoading$;
    this.taskId = +(this.route.snapshot.paramMap.get('id') ?? 0);
    this.loadTask();
  }

  loadTask() {
    this.courseService.getStudentTask(this.taskId).subscribe((resp: any) => {
      this.studentTasks = Array.isArray(resp) ? resp : [resp];
      console.log('Tareas cargadas:', this.studentTasks);
    });
  }

  // viewFile(fileUrl: string) {
  //   console.log('Archivo URL:', fileUrl);
  //   const modal = document.getElementById('fileViewerModal');
  //   const iframe = document.getElementById('fileViewer') as HTMLIFrameElement;

  //   if (modal && iframe) {
  //     iframe.src = fileUrl;
  //     (modal as any).modal('show');
  //   }
  // }
  viewFile(fileUrl: string) {
    console.log('Archivo URL:', fileUrl);
    const modalElement = document.getElementById('fileViewerModal');
    const iframe = document.getElementById('fileViewer') as HTMLIFrameElement;

    if (modalElement && iframe) {
      iframe.src = fileUrl;
      // Usa Bootstrap modal en lugar de jQuery modal
      const modal = new Modal(modalElement);
      modal.show();
    }
  }
}
