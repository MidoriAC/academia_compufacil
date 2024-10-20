import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders, HttpParams } from '@angular/common/http';
import { Observable, BehaviorSubject } from 'rxjs';
import { finalize } from 'rxjs/operators';
import { AuthService } from '../../auth';
import { URL_SERVICIOS } from 'src/app/config/config';

@Injectable({
  providedIn: 'root',
})
export class ReportService {
  isLoading$: Observable<boolean>;
  isLoadingSubject: BehaviorSubject<boolean>;

  constructor(private http: HttpClient, public authService: AuthService) {
    this.isLoadingSubject = new BehaviorSubject<boolean>(false);
    this.isLoading$ = this.isLoadingSubject.asObservable();
  }

  getCourseStatistics(filters: any): Observable<any> {
    this.isLoadingSubject.next(true);
    let headers = new HttpHeaders({
      Authorization: 'Bearer ' + this.authService.token,
    });
    let params = new HttpParams();
    if (filters.startDate)
      params = params.append('start_date', filters.startDate);
    if (filters.endDate) params = params.append('end_date', filters.endDate);
    if (filters.categoryId)
      params = params.append('category_id', filters.categoryId);

    return this.http
      .get(`${URL_SERVICIOS}/reports/course-statistics`, { headers, params })
      .pipe(finalize(() => this.isLoadingSubject.next(false)));
  }

  getStudentStatistics(filters: any): Observable<any> {
    this.isLoadingSubject.next(true);
    let headers = new HttpHeaders({
      Authorization: 'Bearer ' + this.authService.token,
    });
    let params = new HttpParams();
    if (filters.startDate)
      params = params.append('start_date', filters.startDate);
    if (filters.endDate) params = params.append('end_date', filters.endDate);

    return this.http
      .get(`${URL_SERVICIOS}/reports/student-statistics`, { headers, params })
      .pipe(finalize(() => this.isLoadingSubject.next(false)));
  }

  downloadCourseStatistics(filters: any, format: string): Observable<Blob> {
    this.isLoadingSubject.next(true);
    let headers = new HttpHeaders({
      Authorization: 'Bearer ' + this.authService.token,
    });
    let params = new HttpParams();
    if (filters.startDate)
      params = params.append('start_date', filters.startDate);
    if (filters.endDate) params = params.append('end_date', filters.endDate);
    if (filters.categoryId)
      params = params.append('category_id', filters.categoryId);
    params = params.append('format', format);

    return this.http
      .get(`${URL_SERVICIOS}/reports/course-statistics/download`, {
        headers,
        params,
        responseType: 'blob',
      })
      .pipe(finalize(() => this.isLoadingSubject.next(false)));
  }

  downloadStudentStatistics(filters: any, format: string): Observable<Blob> {
    this.isLoadingSubject.next(true);
    let headers = new HttpHeaders({
      Authorization: 'Bearer ' + this.authService.token,
    });
    let params = new HttpParams();
    if (filters.startDate)
      params = params.append('start_date', filters.startDate);
    if (filters.endDate) params = params.append('end_date', filters.endDate);
    params = params.append('format', format);

    return this.http
      .get(`${URL_SERVICIOS}/reports/student-statistics/download`, {
        headers,
        params,
        responseType: 'blob',
      })
      .pipe(finalize(() => this.isLoadingSubject.next(false)));
  }
}
