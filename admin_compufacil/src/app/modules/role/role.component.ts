import { Component, OnInit } from '@angular/core';
import { RoleService } from '././service/role.service';

@Component({
  selector: 'app-role-management',
  templateUrl: './role.component.html',
  styleUrls: ['./role.component.scss'],
})
export class RoleManagementComponent implements OnInit {
  constructor() {}
  ngOnInit(): void {}
  //   roles: any[] = [];

  //   constructor(private roleService: RoleService) {}

  //   ngOnInit() {
  //     this.loadRoles();
  //   }

  //   loadRoles() {
  //     this.roleService.getRoles().subscribe(
  //       (data) => {
  //         this.roles = data;
  //       },
  //       (error) => {
  //         console.error('Error loading roles:', error);
  //       }
  //     );
  //   }

  //   editRole(role: any) {
  //     // Implement edit logic
  //   }

  //   deleteRole(roleId: number) {
  //     this.roleService.deleteRole(roleId).subscribe(
  //       () => {
  //         this.loadRoles();
  //       },
  //       (error) => {
  //         console.error('Error deleting role:', error);
  //       }
  //     );
  //   }
}
