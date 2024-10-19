import { Component, Input, OnInit } from '@angular/core';
import { NgbActiveModal } from '@ng-bootstrap/ng-bootstrap';
import { FormBuilder, FormGroup, Validators, FormArray } from '@angular/forms';
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
        <!-- <div class="form-group">
          <label>Permisos</label>
          <div *ngFor="let permission of permissions; let i = index">
            <label>
              <input type="checkbox" [formControlName]="i" />
              {{ permission.name }}
            </label>
          </div>
        </div> -->
        <div formArrayName="permissions">
          <div *ngFor="let permission of permissions; let i = index">
            <label>
              <input type="checkbox" [formControlName]="i" />
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
    this.loadPermissions();
  }

  loadPermissions() {
    this.roleService.getPermissions().subscribe(
      (data: any) => {
        this.permissions = data;
        this.initForm();
      },
      (error) => {
        console.error('Error loading permissions', error);
      }
    );
  }

  initForm() {
    this.roleForm = this.formBuilder.group({
      name: [this.role.name || '', Validators.required],
      description: [this.role.descripcion || ''],
      permissions: this.formBuilder.array([]),
    });

    this.permissions.forEach((permission) => {
      const control = this.formBuilder.control(
        this.role.permissions
          ? this.role.permissions.includes(permission.id)
          : false
      );
      (this.roleForm.get('permissions') as FormArray).push(control);
    });
  }

  saveRole() {
    if (this.roleForm.valid) {
      const selectedPermissions = this.roleForm.value.permissions
        .map((checked: boolean, i: number) =>
          checked ? this.permissions[i].id : null
        )
        .filter((id: number | null) => id !== null);

      const roleData = {
        name: this.roleForm.value.name,
        description: this.roleForm.value.description,
        permissions: selectedPermissions,
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
