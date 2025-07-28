import { Component, OnInit } from '@angular/core';
import { EmployeService } from 'src/app/services/employe/employe.service';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatButtonModule } from '@angular/material/button';
import { MatTableModule } from '@angular/material/table';
import { MatIconModule } from '@angular/material/icon';
import { MatSnackBar, MatSnackBarModule } from '@angular/material/snack-bar';

@Component({
  selector: 'app-employes',
  templateUrl: './employe-page.component.html',
  standalone: true,
  imports: [
    CommonModule,
    ReactiveFormsModule,
    MatFormFieldModule,
    MatInputModule,
    MatButtonModule,
    MatTableModule,
    MatIconModule,
    MatSnackBarModule
  ],
})
export class EmployesComponent implements OnInit {
  employes: any[] = [];
  employeForm: FormGroup;
  employeEnEdition: any = null;
  displayedColumns: string[] = ['id', 'nom', 'prenom', 'email', 'date_embauche', 'actions'];

  constructor(
    private employeService: EmployeService,
    private fb: FormBuilder,
    private snackBar: MatSnackBar
  ) {
    this.employeForm = this.fb.group({
      nom: ['', Validators.required],
      prenom: ['', Validators.required],
      email: ['', [Validators.required, Validators.email]],
      date_embauche: ['']
    });
  }

  ngOnInit(): void {
    this.chargerEmployes();
  }

  chargerEmployes() {
    this.employeService.getAll().subscribe((res: any) => {
      this.employes = res;
    });
  }

  onSubmit() {
    if (this.employeForm.invalid) return;
    const data = this.employeForm.value;
    if (this.employeEnEdition) {
      this.employeService.update(this.employeEnEdition.id, data).subscribe(() => {
        this.snackBar.open('Employé modifié avec succès', '', { duration: 2000 });
        this.employeEnEdition = null;
        this.employeForm.reset();
        this.chargerEmployes();
      });
    } else {
      this.employeService.create(data).subscribe(() => {
        this.snackBar.open('Employé ajouté avec succès', '', { duration: 2000 });
        this.employeForm.reset();
        this.chargerEmployes();
      });
    }
  }

  editerEmploye(employe: any) {
    this.employeEnEdition = employe;
    this.employeForm.patchValue(employe);
  }

  annulerEdition() {
    this.employeEnEdition = null;
    this.employeForm.reset();
  }

  supprimerEmploye(id: number) {
    if (confirm('Voulez-vous vraiment supprimer cet employé ?')) {
      this.employeService.delete(id).subscribe(() => {
        this.snackBar.open('Employé supprimé', '', { duration: 2000 });
        this.chargerEmployes();
      });
    }
  }
} 