// import { Component } from '@angular/core';
// import { TiendaAuthService } from '../service/tienda-auth.service';
// import { ActivatedRoute, Router } from '@angular/router';
// import { DomSanitizer } from '@angular/platform-browser';

// declare function alertDanger([]): any;
// declare function alertSuccess([]): any;
// @Component({
//   selector: 'app-course-leason',
//   templateUrl: './course-leason.component.html',
//   styleUrls: ['./course-leason.component.css'],
// })
// export class CourseLeasonComponent {
//   slug: any = null;
//   courses_selected: any = null;
//   clase_selected: any = null;
//   constructor(
//     public tiendaAuthService: TiendaAuthService,
//     public activedRoute: ActivatedRoute,
//     public router: Router,
//     public Sanitizer: DomSanitizer
//   ) {}
//   ngOnInit(): void {
//     this.activedRoute.params.subscribe((resp: any) => {
//       console.log(resp);
//       this.slug = resp.slug;
//     });

//     this.tiendaAuthService.showCourse(this.slug).subscribe((resp: any) => {
//       console.log(resp);
//       if (resp.message == 403) {
//         alertDanger(resp.message_text);
//         this.router.navigateByUrl('/');
//       }
//       this.courses_selected = resp.course;

//       this.clase_selected = this.courses_selected.malla[0].clases[0];
//     });
//   }

//   urlVideo(clase_selected: any) {
//     return this.Sanitizer.bypassSecurityTrustResourceUrl(clase_selected.vimeo);
//   }

//   openClase(clase: any) {
//     this.clase_selected = clase;
//   }
// }

import { Component, OnInit } from '@angular/core';
import { TiendaAuthService } from '../service/tienda-auth.service';
import { ActivatedRoute, Router } from '@angular/router';
import { DomSanitizer } from '@angular/platform-browser';

declare function alertDanger([]): any;
declare function alertSuccess([]): any;

@Component({
  selector: 'app-course-leason',
  templateUrl: './course-leason.component.html',
  styleUrls: ['./course-leason.component.css'],
})
export class CourseLeasonComponent implements OnInit {
  slug: any = null;
  courses_selected: any = null;
  clase_selected: any = null;
  progress: number = 0;
  completedClasses: number = 0;
  totalClasses: number = 0;
  activityFile: File | null = null;
  activityComments: string = '';

  constructor(
    public tiendaAuthService: TiendaAuthService,
    public activedRoute: ActivatedRoute,
    public router: Router,
    public Sanitizer: DomSanitizer
  ) {}

  ngOnInit(): void {
    this.activedRoute.params.subscribe((resp: any) => {
      console.log(resp);
      this.slug = resp.slug;
    });

    this.loadCourseData();
  }

  loadCourseData() {
    this.tiendaAuthService.showCourse(this.slug).subscribe(
      (resp: any) => {
        console.log(resp);
        if (resp.message == 403) {
          alertDanger(resp.message_text);
          this.router.navigateByUrl('/');
        }
        this.courses_selected = resp.course;
        this.progress = resp.progress;
        this.completedClasses = resp.completed_classes;
        this.totalClasses = resp.total_classes;

        this.clase_selected = this.courses_selected.malla[0].clases[0];

        if (
          this.courses_selected.malla &&
          this.courses_selected.malla.length > 0 &&
          this.courses_selected.malla[0].clases &&
          this.courses_selected.malla[0].clases.length > 0
        ) {
          this.clase_selected = this.courses_selected.malla[0].clases[0];
        }
      },
      (error) => {
        console.error('Error loading course data:', error);
        alertDanger('Error al cargar los datos del curso');
      }
    );
  }

  urlVideo(clase_selected: any) {
    return this.Sanitizer.bypassSecurityTrustResourceUrl(clase_selected.vimeo);
  }

  openClase(clase: any) {
    this.clase_selected = clase;
  }

  onFileSelected(event: any) {
    this.activityFile = event.target.files[0];
  }

  uploadActivity() {
    if (!this.activityFile) {
      alertDanger('Por favor, selecciona un archivo.');
      return;
    }

    const formData = new FormData();
    formData.append('file', this.activityFile);
    formData.append('comments', this.activityComments);
    formData.append('course_id', this.courses_selected.id);
    formData.append('class_id', this.clase_selected.id);

    this.tiendaAuthService.uploadActivity(formData).subscribe(
      (resp: any) => {
        alertSuccess('Actividad subida exitosamente');
        this.activityFile = null;
        this.activityComments = '';
      },
      (error) => {
        alertDanger('Error al subir la actividad');
      }
    );
  }
}
