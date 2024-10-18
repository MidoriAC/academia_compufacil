import { Component, Input, OnInit } from '@angular/core';
import { NgbActiveModal } from '@ng-bootstrap/ng-bootstrap';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { RoleService } from '../service/role.service';

@Component({
  selector: 'app-role-modal',
  template: `
    <div class="modal-header">
      <h4 class="modal-title">{{ role.id ? 'Editar' : 'Crear' }} Rol</h4>
      <button
        type="button"
        class="close"
        aria-label="Close"
        (click)="activeModal.dismiss('Cross click')"
      >
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
    <div class="modal-body">
      <form [formGroup]="roleForm">
        <div class="form-group">
          <label for="name">Nombre</label>
          <input
            type="text"
            class="form-control"
            id="name"
            formControlName="name"
          />
        </div>
        <div class="form-group">
          <label for="description">Descripción</label>
          <textarea
            class="form-control"
            id="description"
            formControlName="description"
          ></textarea>
        </div>
        <div class="form-group">
          <label>Permisos</label>
          <div *ngFor="let permission of permissions">
            <label>
              <input
                type="checkbox"
                [value]="permission.id"
                (change)="onPermissionChange($event)"
              />
              {{ permission.name }}
            </label>
          </div>
        </div>
      </form>
    </div>
    <div class="modal-footer">
      <button
        type="button"
        class="btn btn-outline-dark"
        (click)="activeModal.close('Close click')"
      >
        Cerrar
      </button>
      <button
        type="button"
        class="btn btn-primary"
        (click)="saveRole()"
        [disabled]="roleForm.invalid"
      >
        Guardar
      </button>
    </div>
  `,
})
// export class RoleModalComponent implements OnInit {
//   @Input() role: any;
//   @Input() permissions: any[];

//   roleForm: FormGroup;
//   selectedPermissions: number[] = [];

//   constructor(
//     public activeModal: NgbActiveModal,
//     private formBuilder: FormBuilder,
//     private roleService: RoleService
//   ) {}

//   ngOnInit() {
//     this.roleForm = this.formBuilder.group({
//       name: [this.role.name || '', Validators.required],
//       description: [this.role.description || ''],
//     });

//     if (this.role.permissions) {
//       this.selectedPermissions = this.role.permissions.split(',').map(Number);
//     }
//   }

//   onPermissionChange(event: any) {
//     const permissionId = Number(event.target.value);
//     if (event.target.checked) {
//       this.selectedPermissions.push(permissionId);
//     } else {
//       const index = this.selectedPermissions.indexOf(permissionId);
//       if (index > -1) {
//         this.selectedPermissions.splice(index, 1);
//       }
//     }
//   }

//   saveRole() {
//     if (this.roleForm.valid) {
//       const roleData = {
//         ...this.roleForm.value,
//         permissions: this.selectedPermissions,
//       };

//       if (this.role.id) {
//         this.roleService.updateRole(this.role.id, roleData).subscribe(
//           (response) => {
//             this.activeModal.close(response);
//           },
//           (error) => {
//             console.error('Error updating role', error);
//           }
//         );
//       } else {
//         this.roleService.createRole(roleData).subscribe(
//           (response) => {
//             this.activeModal.close(response);
//           },
//           (error) => {
//             console.error('Error creating role', error);
//           }
//         );
//       }
//     }
//   }
// }
export class RoleModalComponent implements OnInit {
  @Input() role: any;
  permissions: any[] = [];
  roleForm: FormGroup;

  constructor(
    public activeModal: NgbActiveModal,
    private formBuilder: FormBuilder,
    private roleService: RoleService
  ) {}

  ngOnInit() {
    this.roleForm = this.formBuilder.group({
      name: [this.role.name || '', Validators.required],
      description: [this.role.description || ''],
    });

    this.loadPermissions();
  }

  loadPermissions() {
    this.roleService.getPermissions().subscribe(
      (data: any) => {
        this.permissions = data.map((permission: any) => ({
          ...permission,
          checked: this.role.permissions
            ? this.role.permissions.includes(permission.name)
            : false,
        }));
      },
      (error) => {
        console.error('Error loading permissions', error);
      }
    );
  }

  onPermissionChange(permission: any) {
    permission.checked = !permission.checked;
  }

  //   saveRole() {
  //     if (this.roleForm.valid) {
  //       const roleData = {
  //         ...this.roleForm.value,
  //         permissions: this.permissions
  //           .filter((p) => p.checked)
  //           .map((p) => p.name),
  //       };

  //       if (this.role.id) {
  //         this.roleService.updateRole(this.role.id, roleData).subscribe(
  //           (response) => {
  //             this.activeModal.close(response);
  //           },
  //           (error) => {
  //             console.error('Error updating role', error);
  //           }
  //         );
  //       } else {
  //         this.roleService.createRole(roleData).subscribe(
  //           (response) => {
  //             this.activeModal.close(response);
  //           },
  //           (error) => {
  //             console.error('Error creating role', error);
  //           }
  //         );
  //       }
  //     }
  //   }
  saveRole() {
    if (this.roleForm.valid) {
      const roleData = {
        ...this.roleForm.value,
        permissions: this.permissions.filter((p) => p.checked).map((p) => p.id),
      };

      console.log('Sending role data:', roleData); // Para depuración

      if (this.role.id) {
        this.roleService.updateRole(this.role.id, roleData).subscribe(
          (response) => {
            console.log('Role updated:', response);
            this.activeModal.close(response);
          },
          (error) => {
            console.error('Error updating role', error);
          }
        );
      } else {
        this.roleService.createRole(roleData).subscribe(
          (response) => {
            console.log('Role created:', response);
            this.activeModal.close(response);
          },
          (error) => {
            console.error('Error creating role', error);
          }
        );
      }
    }
  }
}
