import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { catchError, Observable, throwError } from 'rxjs';
import { URL_SERVICIOS } from 'src/app/config/config';

@Injectable({
  providedIn: 'root',
})
export class RoleService {
  constructor(private http: HttpClient) {}

  getRoles(): Observable<any> {
    return this.http.get(`${URL_SERVICIOS}/roles`);
  }

  getRole(id: number): Observable<any> {
    return this.http.get<any>(`${URL_SERVICIOS}/roles/${id}`);
  }

  createRole(roleData: any): Observable<any> {
    return this.http.post(`${URL_SERVICIOS}/roles`, roleData);
  }

  // updateRole(roleId: number, roleData: any): Observable<any> {
  //   return this.http.put(`${URL_SERVICIOS}/roles/${roleId}`, roleData);
  // }
  updateRole(roleId: number, roleData: any): Observable<any> {
    return this.http.put(`${URL_SERVICIOS}/roles/${roleId}`, roleData).pipe(
      catchError((error) => {
        console.error('Error updating role:', error);
        return throwError('Error updating role. Please try again.');
      })
    );
  }

  deleteRole(roleId: number): Observable<any> {
    return this.http.delete(`${URL_SERVICIOS}/roles/${roleId}`);
  }

  getPermissions(): Observable<any[]> {
    return this.http.get<any[]>(`${URL_SERVICIOS}/permissions`);
  }

  getUserRole(userId: number): Observable<any> {
    return this.http.get<any>(`${URL_SERVICIOS}/users/${userId}/role`);
  }

  getUserPermissions(userId: number): Observable<any[]> {
    return this.http.get<any[]>(`${URL_SERVICIOS}/users/${userId}/permissions`);
  }
  checkPermission(permission: string): Observable<boolean> {
    return this.http.get<boolean>(
      `${URL_SERVICIOS}/check-permission/${permission}`
    );
  }
}
