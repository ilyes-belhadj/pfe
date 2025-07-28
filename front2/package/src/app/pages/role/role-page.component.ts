import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';
import { MatSnackBar, MatSnackBarModule } from '@angular/material/snack-bar';
import { MatDialog, MatDialogModule } from '@angular/material/dialog';
import { RoleService } from 'src/app/services/role/role.service';
import { MaterialModule } from '../../material.module';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

@Component({
  selector: 'app-role-page',
  templateUrl: './role-page.component.html',
  styleUrls: ['./role-page.component.scss'],
  standalone: true,
  imports: [
    CommonModule,
    FormsModule,
    ReactiveFormsModule,
    MaterialModule,
    MatSnackBarModule,
    MatDialogModule,
  ],
})
export class RolePageComponent implements OnInit {
  roles: any[] = [];
  roleForm: FormGroup;
  roleEnEdition: any = null;
  displayedColumns = ['nom', 'description', 'actions'];

  constructor(
    private fb: FormBuilder,
    private roleService: RoleService,
    private snackBar: MatSnackBar,
    private dialog: MatDialog
  ) {
    this.roleForm = this.fb.group({
      nom: ['', [Validators.required, Validators.minLength(2)]],
      description: ['', Validators.required],
      // Ajoute ici les autres champs nécessaires
    });
  }

  ngOnInit() {
    this.chargerRoles();
  }

  chargerRoles() {
    this.roleService.getAll().subscribe(roles => this.roles = roles);
  }

  onSubmit() {
    if (this.roleForm.invalid) return;
    const data = this.roleForm.value;
    if (this.roleEnEdition) {
      this.roleService.update(this.roleEnEdition.id, data).subscribe(() => {
        this.snackBar.open('Rôle modifié avec succès', '', { duration: 2000 });
        this.annulerEdition();
        this.chargerRoles();
      });
    } else {
      this.roleService.create(data).subscribe(() => {
        this.snackBar.open('Rôle ajouté avec succès', '', { duration: 2000 });
        this.roleForm.reset();
        this.chargerRoles();
      });
    }
  }

  editerRole(role: any) {
    this.roleEnEdition = role;
    this.roleForm.patchValue(role);
  }

  annulerEdition() {
    this.roleEnEdition = null;
    this.roleForm.reset();
  }

  confirmerSuppression(role: any) {
    if (confirm('Voulez-vous vraiment supprimer ce rôle ?')) {
      this.supprimerRole(role.id);
    }
  }

  supprimerRole(id: number) {
    this.roleService.delete(id).subscribe(() => {
      this.snackBar.open('Rôle supprimé', '', { duration: 2000 });
      this.chargerRoles();
    });
  }
} 