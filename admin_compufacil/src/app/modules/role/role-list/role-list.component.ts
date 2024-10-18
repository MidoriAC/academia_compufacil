import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import { RoleService } from '../service/role.service';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { RoleModalComponent } from './role-modal.component';

@Component({
  selector: 'app-role-list',
  template: `
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Roles</h3>
        <div class="card-toolbar">
          <button class="btn btn-primary" (click)="openRoleModal()">
            Nuevo Rol
          </button>
        </div>
      </div>
      <div class="card-body" *ngIf="roles.length > 0; else noRoles">
        <div class="card-body">
          <table class="table">
            <thead>
              <tr>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Permisos</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <tr *ngFor="let role of roles">
                <td>{{ role.name }}</td>
                <td>
                  {{ role.descripcion ? role.descripcion : 'Sin descripción' }}
                </td>
                <td>
                  {{ role.permissions ? role.permissions : 'No asignado' }}
                </td>
                <td>
                  <button
                    class="btn btn-sm btn-info mr-2"
                    (click)="editRole(role)"
                  >
                    Editar
                  </button>
                  <button
                    class="btn btn-sm btn-danger"
                    (click)="deleteRole(role.id)"
                  >
                    Eliminar
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <ng-template #noRoles>
        <p>Cargando roles...</p>
      </ng-template>
    </div>
  `,
})
export class RoleListComponent implements OnInit {
  roles: any[] = [];
  permissions: any[] = [];

  constructor(
    private roleService: RoleService,
    private modalService: NgbModal,
    private cdr: ChangeDetectorRef
  ) {}

  ngOnInit() {
    this.loadRoles();
    this.loadPermissions();
  }

  loadRoles() {
    this.roleService.getRoles().subscribe(
      (data: any) => {
        console.log(data);
        this.roles = data;
        this.cdr.detectChanges();
      },
      (error) => {
        console.error('Error loading roles', error);
      }
    );
  }

  loadPermissions() {
    this.roleService.getPermissions().subscribe(
      (data: any) => {
        this.permissions = data.data;
      },
      (error) => {
        console.error('Error loading permissions', error);
      }
    );
  }

  openRoleModal(role?: any) {
    const modalRef = this.modalService.open(RoleModalComponent);
    modalRef.componentInstance.role = role || {};
    modalRef.componentInstance.permissions = this.permissions;
    modalRef.result.then((result) => {
      if (result) {
        this.loadRoles();
      }
    });
  }

  editRole(role: any) {
    this.openRoleModal(role);
  }

  deleteRole(roleId: number) {
    if (confirm('¿Estás seguro de que quieres eliminar este rol?')) {
      this.roleService.deleteRole(roleId).subscribe(
        () => {
          this.loadRoles();
        },
        (error) => {
          console.error('Error deleting role', error);
        }
      );
    }
  }
}
