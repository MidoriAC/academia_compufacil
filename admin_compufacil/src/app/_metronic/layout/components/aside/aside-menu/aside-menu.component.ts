import { Component, OnInit, OnDestroy } from '@angular/core';
import { AuthService } from 'src/app/modules/auth/services/auth.service';
import { Subscription } from 'rxjs';

@Component({
  selector: 'app-aside-menu',
  templateUrl: './aside-menu.component.html',
  styleUrls: ['./aside-menu.component.scss'],
})
export class AsideMenuComponent implements OnInit, OnDestroy {
  userPermissions: { [key: string]: boolean } = {};
  private permissionSubscription: Subscription;

  constructor(public authService: AuthService) {}

  ngOnInit(): void {
    this.permissionSubscription = this.authService.userPermissions$.subscribe(
      (permissions) => {
        console.log(
          'Permisos actualizados en AsideMenuComponent:',
          permissions
        );
        this.updatePermissions(permissions);
      }
    );
  }

  private updatePermissions(permissions: string[]) {
    const permissionsToCheck = [
      'view_courses',
      'create_course',
      'view_users',
      'rol_management',
      'view_categories',
      'tasks_students',
      'cupon_register',
      'cupons_view',
      'discount',
      'discount_list',
      'activation_courses',
      'upload_certificate',
      'estadisticas_estudiante',
      'estadisticas_curso',
    ];
    permissionsToCheck.forEach((permission) => {
      this.userPermissions[permission] = permissions.includes(permission);
    });
    console.log('Permisos actualizados:', this.userPermissions);
  }

  ngOnDestroy() {
    if (this.permissionSubscription) {
      this.permissionSubscription.unsubscribe();
    }
  }
}
