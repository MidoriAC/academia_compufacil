import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable, BehaviorSubject } from 'rxjs';
import { finalize, map } from 'rxjs/operators';
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

  getDashboardStats(): Observable<any> {
    this.isLoadingSubject.next(true);
    let headers = new HttpHeaders({
      Authorization: 'Bearer ' + this.authService.token,
    });

    let URL = URL_SERVICIOS + '/dashboard-stats';

    return this.http.get(URL, { headers: headers }).pipe(
      map((response: any) => {
        // Transformar los datos de ventas mensuales al formato requerido por el gráfico
        const monthlySalesData = Object.entries(response.monthly_sales).map(
          ([month, total]) => ({
            x: month,
            y: parseFloat(total as string) || 0,
          })
        );

        return {
          ...response,
          monthly_sales: monthlySalesData,
        };
      }),
      finalize(() => this.isLoadingSubject.next(false))
    );
  }
  downloadMonthlySalesReport(): Observable<Blob> {
    const headers = new HttpHeaders({
      Authorization: 'Bearer ' + this.authService.token,
    });

    return this.http.get(URL_SERVICIOS + '/monthly-sales-report', {
      headers,
      responseType: 'blob',
    });
  }
}
