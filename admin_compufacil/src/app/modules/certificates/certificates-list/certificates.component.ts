import {
  Component,
  OnInit,
  ChangeDetectorRef,
  ViewChild,
  TemplateRef,
} from '@angular/core';
import { CertificateService } from '../service/certificate.service';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';

@Component({
  selector: 'app-student-certificates',
  template: `
    <div class="card">
      <div class="card-header border-0 pt-6">
        <div class="card-title">
          <h3>Certificados de Estudiantes</h3>
        </div>
      </div>
      <div class="card-body py-4">
        <table
          class="table align-middle table-row-dashed fs-6 gy-5"
          id="kt_table_students"
        >
          <thead>
            <tr
              class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0"
            >
              <th class="min-w-125px">Estudiante</th>
              <th class="min-w-125px">Email</th>
              <th class="text-end min-w-100px">Acciones</th>
            </tr>
          </thead>
          <tbody class="text-gray-600 fw-bold">
            <tr *ngFor="let student of students">
              <td>{{ student.name }}</td>
              <td>{{ student.email }}</td>
              <td class="text-end">
                <button
                  class="btn btn-sm btn-primary"
                  (click)="showStudentCourses(student)"
                >
                  Ver Cursos
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <ng-template #coursesModal let-modal>
      <div class="modal-header">
        <h4 class="modal-title" id="modal-basic-title">
          Cursos de {{ selectedStudent?.name }}
        </h4>
        <button
          type="button"
          class="close"
          aria-label="Close"
          (click)="modal.dismiss('Cross click')"
        >
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <table class="table">
          <thead>
            <tr>
              <th>Curso</th>
              <th>Estado del Certificado</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr *ngFor="let course of selectedStudent?.courses">
              <td>{{ course.course_title }}</td>
              <td>
                <span *ngIf="course.certificate_id">Certificado Emitido</span>
                <span *ngIf="!course.certificate_id">Sin Certificado</span>
              </td>
              <td>
                <button
                  class="btn btn-sm btn-primary"
                  (click)="openCertificateModal(course)"
                >
                  {{
                    course.certificate_id
                      ? 'Actualizar Certificado'
                      : 'Subir Certificado'
                  }}
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </ng-template>

    <ng-template #certificateModal let-modal>
      <div class="modal-header">
        <h4 class="modal-title" id="modal-basic-title">
          {{
            selectedCourse?.certificate_id
              ? 'Actualizar Certificado'
              : 'Subir Certificado'
          }}
        </h4>
        <button
          type="button"
          class="close"
          aria-label="Close"
          (click)="modal.dismiss('Cross click')"
        >
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>Estudiante: {{ selectedStudent?.name }}</p>
        <p>Curso: {{ selectedCourse?.course_title }}</p>
        <div class="form-group">
          <label for="certificate">Seleccionar archivo de certificado</label>
          <input
            type="file"
            class="form-control-file"
            id="certificate"
            (change)="onFileSelected($event)"
            accept=".pdf,.jpg,.jpeg,.png"
          />
        </div>
      </div>
      <div class="modal-footer">
        <button
          type="button"
          class="btn btn-outline-dark"
          (click)="modal.close('Save click')"
        >
          Cancelar
        </button>
      </div>
    </ng-template>
  `,
})
export class StudentCertificatesComponent implements OnInit {
  @ViewChild('coursesModal') coursesModal!: TemplateRef<any>;
  @ViewChild('certificateModal') certificateModal!: TemplateRef<any>;

  students: any[] = [];
  isLoading$: any;
  selectedStudent: any;
  selectedCourse: any;

  constructor(
    public certificateService: CertificateService,
    public modalService: NgbModal,
    private cdr: ChangeDetectorRef
  ) {}

  ngOnInit(): void {
    this.isLoading$ = this.certificateService.isLoading$;
    this.loadStudents();
  }

  loadStudents() {
    this.certificateService.getStudentsWithCertificates().subscribe(
      (resp: any) => {
        this.students = resp.students;
        console.log('Estudiantes cargados:', this.students);
        this.cdr.detectChanges();
      },
      (error) => {
        console.error('Error al cargar los estudiantes:', error);
      }
    );
  }

  showStudentCourses(student: any) {
    this.selectedStudent = student;
    this.modalService.open(this.coursesModal, { size: 'lg' });
  }

  openCertificateModal(course: any) {
    this.selectedCourse = course;
    this.modalService.open(this.certificateModal, {
      ariaLabelledBy: 'modal-basic-title',
    });
  }

  onFileSelected(event: any) {
    const file = event.target.files[0];
    if (file) {
      if (this.selectedCourse.certificate_id) {
        this.updateCertificate(file);
      } else {
        this.uploadCertificate(file);
      }
    }
  }

  uploadCertificate(file: File) {
    this.certificateService
      .uploadCertificate(
        this.selectedStudent.id,
        this.selectedCourse.course_id,
        file
      )
      .subscribe(
        (response: any) => {
          console.log('Certificado subido exitosamente');
          this.modalService.dismissAll();
          this.loadStudents();
        },
        (error) => {
          console.error('Error al subir el certificado', error);
        }
      );
  }

  updateCertificate(file: File) {
    this.certificateService
      .updateCertificate(this.selectedCourse.certificate_id, file)
      .subscribe(
        (response: any) => {
          console.log('Certificado actualizado exitosamente');
          this.modalService.dismissAll();
          this.loadStudents();
        },
        (error) => {
          console.error('Error al actualizar el certificado', error);
        }
      );
  }
}
