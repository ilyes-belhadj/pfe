import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';
import { MatSnackBar, MatSnackBarModule } from '@angular/material/snack-bar';
import { MatDialog, MatDialogModule } from '@angular/material/dialog';
import { UserService } from 'src/app/services/user/user.service';
import { MaterialModule } from '../../material.module';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

@Component({
  selector: 'app-user-page',
  templateUrl: './user-page.component.html',
  styleUrls: ['./user-page.component.scss'],
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
export class UserPageComponent implements OnInit {
  users: any[] = [];
  userForm: FormGroup;
  userEnEdition: any = null;
  displayedColumns = ['nom', 'prenom', 'email', 'role_id', 'actions'];

  constructor(
    private fb: FormBuilder,
    private userService: UserService,
    private snackBar: MatSnackBar,
    private dialog: MatDialog
  ) {
    this.userForm = this.fb.group({
      nom: ['', [Validators.required, Validators.minLength(2)]],
      prenom: ['', [Validators.required, Validators.minLength(2)]],
      email: ['', [Validators.required, Validators.email]],
      role_id: ['', Validators.required],
      // Ajoute ici les autres champs nécessaires
    });
  }

  ngOnInit() {
    this.chargerUsers();
  }

  chargerUsers() {
    this.userService.getAll().subscribe(users => this.users = users);
  }

  onSubmit() {
    if (this.userForm.invalid) return;
    const data = this.userForm.value;
    if (this.userEnEdition) {
      this.userService.update(this.userEnEdition.id, data).subscribe(() => {
        this.snackBar.open('Utilisateur modifié avec succès', '', { duration: 2000 });
        this.annulerEdition();
        this.chargerUsers();
      });
    } else {
      this.userService.create(data).subscribe({
        next: () => {
          this.snackBar.open('Utilisateur ajouté avec succès', '', { duration: 2000 });
          this.userForm.reset();
          this.chargerUsers();
        },
        error: (err) => {
          this.snackBar.open('Erreur lors de l\'ajout : ' + (err?.error?.message || 'Vérifie les champs ou l\'API'), '', { duration: 3000 });
          console.error(err);
        }
      });
    }
  }

  editerUser(user: any) {
    this.userEnEdition = user;
    this.userForm.patchValue(user);
  }

  annulerEdition() {
    this.userEnEdition = null;
    this.userForm.reset();
  }

  confirmerSuppression(user: any) {
    if (confirm('Voulez-vous vraiment supprimer cet utilisateur ?')) {
      this.supprimerUser(user.id);
    }
  }

  supprimerUser(id: number) {
    this.userService.delete(id).subscribe(() => {
      this.snackBar.open('Utilisateur supprimé', '', { duration: 2000 });
      this.chargerUsers();
    });
  }
} 