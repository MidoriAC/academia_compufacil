import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable, BehaviorSubject } from 'rxjs';
import { finalize } from 'rxjs/operators';
import { AuthService } from '../../../../modules/auth';
import { URL_SERVICIOS } from 'src/app/config/config';

@Injectable({
  providedIn: 'root',
})
export class DashboardService {
  isLoading$: Observable<boolean>;
  isLoadingSubject: BehaviorSubject<boolean>;

  constructor(private http: HttpClient, public authService: AuthService) {
    this.isLoadingSubject = new BehaviorSubject<boolean>(false);
    this.isLoading$ = this.isLoadingSubject.asObservable();
  }

  getDashboardStats() {
    this.isLoadingSubject.next(true);
    let headers = new HttpHeaders({
      Authorization: 'Bearer ' + this.authService.token,
    });

    let URL = URL_SERVICIOS + '/dashboard-stats';

    return this.http
      .get(URL, { headers: headers })
      .pipe(finalize(() => this.isLoadingSubject.next(false)));
  }
}
