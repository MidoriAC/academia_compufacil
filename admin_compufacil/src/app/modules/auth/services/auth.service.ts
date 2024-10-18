import { Injectable, OnDestroy } from '@angular/core';
import { Observable, BehaviorSubject, of, Subscription } from 'rxjs';
import { map, catchError, switchMap, finalize, tap } from 'rxjs/operators';
import { UserModel } from '../models/user.model';
import { AuthModel } from '../models/auth.model';
import { AuthHTTPService } from './auth-http';
import { environment } from 'src/environments/environment';
import { Router } from '@angular/router';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { URL_SERVICIOS } from 'src/app/config/config';

export type UserType = UserModel | undefined;

@Injectable({
  providedIn: 'root',
})
export class AuthService implements OnDestroy {
  // private fields
  private unsubscribe: Subscription[] = []; // Read more: => https://brianflove.com/2016/12/11/anguar-2-unsubscribe-observables/
  private authLocalStorageToken = `${environment.appVersion}-${environment.USERDATA_KEY}`;

  private userPermissions: string[] = [];
  private userPermissionsSubject = new BehaviorSubject<string[]>([]);
  userPermissions$ = this.userPermissionsSubject.asObservable();
  private permissionsLoaded = false;
  // public fields
  currentUser$: Observable<UserType>;
  isLoading$: Observable<boolean>;
  currentUserSubject: BehaviorSubject<UserType>;
  isLoadingSubject: BehaviorSubject<boolean>;

  get currentUserValue(): UserType {
    return this.currentUserSubject.value;
  }

  set currentUserValue(user: UserType) {
    this.currentUserSubject.next(user);
  }

  token: any = null;
  user: any = null;
  constructor(
    private authHttpService: AuthHTTPService,
    private router: Router,
    private http: HttpClient
  ) {
    this.isLoadingSubject = new BehaviorSubject<boolean>(false);
    this.currentUserSubject = new BehaviorSubject<UserType>(undefined);
    this.currentUser$ = this.currentUserSubject.asObservable();
    this.isLoading$ = this.isLoadingSubject.asObservable();
    const subscr = this.getUserByToken().subscribe();
    this.unsubscribe.push(subscr);
    this.getUserPermissions().subscribe();
  }

  // getUserPermissions(): Observable<string[]> {
  //   const userId = this.currentUserValue?.id;
  //   if (!userId) {
  //     return of([]);
  //   }
  //   return this.http
  //     .get<string[]>(`${URL_SERVICIOS}/users/${userId}/permissions`)
  //     .pipe(
  //       map((permissions: string[]) => {
  //         this.userPermissions = permissions;
  //         return permissions;
  //       }),
  //       catchError((error) => {
  //         console.error('Error fetching user permissions', error);
  //         return of([]);
  //       })
  //     );
  // }
  getUserPermissions(): Observable<string[]> {
    if (this.permissionsLoaded) {
      return of(this.userPermissions);
    }

    const user = this.currentUserSubject.value;
    if (!user || !user.id) {
      console.error('No hay usuario logueado o no tiene ID');
      return of([]);
    }

    console.log('Obteniendo permisos para el usuario:', user.id);

    return this.http
      .get<string[]>(`${URL_SERVICIOS}/users/${user.id}/permissions`)
      .pipe(
        tap((permissions: string[]) => {
          console.log('Permisos obtenidos:', permissions);
          this.userPermissions = permissions;
          this.userPermissionsSubject.next(permissions);
          this.permissionsLoaded = true;
        }),
        catchError((error) => {
          console.error('Error fetching user permissions', error);
          return of([]);
        })
      );
  }

  hasPermission(permission: string): Observable<boolean> {
    return this.userPermissions$.pipe(
      map((permissions) => {
        const hasPermission = permissions.includes(permission);
        console.log(
          `Verificando permiso: ${permission}, Resultado: ${hasPermission}`
        );
        return hasPermission;
      })
    );
  }

  // public methods
  // login(email: string, password: string): Observable<any> {
  //   this.isLoadingSubject.next(true);
  //   return this.http
  //     .post(URL_SERVICIOS + '/auth/login', { email: email, password: password })
  //     .pipe(
  //       map((auth: any) => {
  //         console.log(auth);
  //         const result = this.setAuthFromLocalStorage(auth);
  //         return result;
  //       }),
  //       catchError((err) => {
  //         console.error('err', err);
  //         return of(undefined);
  //       }),
  //       finalize(() => this.isLoadingSubject.next(false))
  //     );
  // }
  // login(email: string, password: string): Observable<any> {
  //   this.isLoadingSubject.next(true);
  //   return this.http
  //     .post(URL_SERVICIOS + '/auth/login', { email: email, password: password })
  //     .pipe(
  //       map((auth: any) => {
  //         console.log(auth);
  //         const result = this.setAuthFromLocalStorage(auth);
  //         if (result) {
  //           this.getUserPermissions().subscribe();
  //         }
  //         return result;
  //       }),
  //       catchError((err) => {
  //         console.error('err', err);
  //         return of(undefined);
  //       }),
  //       finalize(() => this.isLoadingSubject.next(false))
  //     );
  // }
  login(email: string, password: string): Observable<any> {
    this.isLoadingSubject.next(true);
    return this.http
      .post(URL_SERVICIOS + '/auth/login', { email, password })
      .pipe(
        switchMap((auth: any) => {
          console.log('Respuesta de login:', auth);
          if (auth && auth.user) {
            this.setAuthFromLocalStorage(auth);
            this.permissionsLoaded = false; // Reset permissions loaded flag
            return this.getUserPermissions().pipe(
              tap(() => console.log('Permisos obtenidos después del login')),
              map(() => auth)
            );
          }
          return of(auth);
        }),
        catchError((err) => {
          console.error('Error en login', err);
          return of(undefined);
        }),
        finalize(() => this.isLoadingSubject.next(false))
      );
  }

  logout() {
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    this.router.navigate(['/auth/login'], {
      queryParams: {},
    });
  }

  getUserByToken(): Observable<any> {
    const auth = this.getAuthFromLocalStorage();
    if (!auth) {
      return of(undefined);
    }

    this.isLoadingSubject.next(true);
    return of(auth).pipe(
      map((user: any) => {
        if (user) {
          this.currentUserSubject.next(user);
        } else {
          this.logout();
        }
        return user;
      }),
      finalize(() => this.isLoadingSubject.next(false))
    );
  }

  // need create new user then login
  registration(user: UserModel): Observable<any> {
    this.isLoadingSubject.next(true);
    return this.authHttpService.createUser(user).pipe(
      map(() => {
        this.isLoadingSubject.next(false);
      }),
      switchMap(() => this.login(user.email, user.password)),
      catchError((err) => {
        console.error('err', err);
        return of(undefined);
      }),
      finalize(() => this.isLoadingSubject.next(false))
    );
  }

  forgotPassword(email: string): Observable<boolean> {
    this.isLoadingSubject.next(true);
    return this.authHttpService
      .forgotPassword(email)
      .pipe(finalize(() => this.isLoadingSubject.next(false)));
  }

  // private methods
  // private setAuthFromLocalStorage(auth: any): boolean {
  //   // store auth authToken/refreshToken/epiresIn in local storage to keep user logged in between page refreshes
  //   if (auth && auth.access_token) {
  //     localStorage.setItem('token', auth.access_token);
  //     localStorage.setItem('user', JSON.stringify(auth.user));
  //     return true;
  //   }
  //   return false;
  // }
  private setAuthFromLocalStorage(auth: any): boolean {
    if (auth && auth.access_token && auth.user) {
      localStorage.setItem('token', auth.access_token);
      localStorage.setItem('user', JSON.stringify(auth.user));
      // Actualiza el currentUserSubject con el usuario completo
      this.currentUserSubject.next(auth.user);
      return true;
    }
    return false;
  }

  // private getAuthFromLocalStorage(): any {
  //   try {
  //     const lsValue = localStorage.getItem('user');
  //     if (!lsValue) {
  //       return undefined;
  //     }
  //     this.token = localStorage.getItem('token');
  //     this.user = JSON.parse(lsValue);
  //     const authData = JSON.parse(lsValue);
  //     return authData;
  //   } catch (error) {
  //     console.error(error);
  //     return undefined;
  //   }
  // }
  private getAuthFromLocalStorage(): any {
    try {
      const userStr = localStorage.getItem('user');
      if (!userStr) {
        return undefined;
      }
      const user = JSON.parse(userStr);
      this.token = localStorage.getItem('token');
      return user;
    } catch (error) {
      console.error(error);
      return undefined;
    }
  }
  ngOnDestroy() {
    this.unsubscribe.forEach((sb) => sb.unsubscribe());
  }
}
