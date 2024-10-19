import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable, BehaviorSubject, finalize } from 'rxjs';
import { AuthService } from '../../auth';
import { URL_SERVICIOS } from 'src/app/config/config';

@Injectable({
  providedIn: 'root',
})
export class CertificateService {
  isLoading$: Observable<boolean>;
  isLoadingSubject: BehaviorSubject<boolean>;

  constructor(private http: HttpClient, public authservice: AuthService) {
    this.isLoadingSubject = new BehaviorSubject<boolean>(false);
    this.isLoading$ = this.isLoadingSubject.asObservable();
  }

  getStudentsWithCertificates() {
    this.isLoadingSubject.next(true);
    let headers = new HttpHeaders({
      Authorization: 'Bearer ' + this.authservice.token,
    });
    let URL = URL_SERVICIOS + '/students-certificates';
    return this.http
      .get(URL, { headers: headers })
      .pipe(finalize(() => this.isLoadingSubject.next(false)));
  }

  uploadCertificate(userId: number, courseId: number, file: File) {
    this.isLoadingSubject.next(true);
    let headers = new HttpHeaders({
      Authorization: 'Bearer ' + this.authservice.token,
    });
    let URL = URL_SERVICIOS + '/certificates';

    const formData = new FormData();
    formData.append('user_id', userId.toString());
    formData.append('course_id', courseId.toString());
    formData.append('certificate', file);

    return this.http
      .post(URL, formData, { headers: headers })
      .pipe(finalize(() => this.isLoadingSubject.next(false)));
  }

  updateCertificate(certificateId: number, file: File) {
    this.isLoadingSubject.next(true);
    let headers = new HttpHeaders({
      Authorization: 'Bearer ' + this.authservice.token,
    });
    let URL = URL_SERVICIOS + '/certificates/' + certificateId;

    const formData = new FormData();
    formData.append('certificate', file);

    return this.http
      .post(URL, formData, { headers: headers })
      .pipe(finalize(() => this.isLoadingSubject.next(false)));
  }
}
