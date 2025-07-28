import { Component, OnInit } from '@angular/core';
import { AbsenceService } from 'src/app/services/absence/absence.service';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatButtonModule } from '@angular/material/button';
import { MatTableModule } from '@angular/material/table';
import { MatIconModule } from '@angular/material/icon';
import { MatSnackBar, MatSnackBarModule } from '@angular/material/snack-bar';
import { MatDatepickerModule } from '@angular/material/datepicker';
import { MatNativeDateModule } from '@angular/material/core';
import { EmployeService } from 'src/app/services/employe/employe.service';
import { MatSelectModule } from '@angular/material/select';
import { MatOptionModule } from '@angular/material/core';

@Component({
  selector: 'app-absence',
  templateUrl: './absence-page.component.html',
  standalone: true,
  imports: [
    CommonModule,
    ReactiveFormsModule,
    MatFormFieldModule,
    MatInputModule,
    MatButtonModule,
    MatTableModule,
    MatIconModule,
    MatSnackBarModule,
    MatDatepickerModule,
    MatNativeDateModule,
    MatSelectModule,
    MatOptionModule
  ],
})
export class AbsencePageComponent implements OnInit {
  absences: any[] = [];
  absenceForm: FormGroup;
  absenceEnEdition: any = null;
  displayedColumns: string[] = ['id', 'employe_id', 'date_debut', 'date_fin', 'motif', 'statut', 'actions'];
  employes: any[] = [];

  constructor(
    private absenceService: AbsenceService,
    private fb: FormBuilder,
    private snackBar: MatSnackBar,
    private employeService: EmployeService
  ) {
    this.absenceForm = this.fb.group({
      employe_id: ['', Validators.required],
      date_debut: ['', Validators.required],
      date_fin: ['', Validators.required],
      motif: ['', Validators.required],
      statut: ['']
    }, { validators: this.dateFinApresDebut });
  }

  ngOnInit(): void {
    this.chargerAbsences();
    this.employeService.getAll().subscribe((res: any) => {
      this.employes = res;
    });
  }

  dateFinApresDebut(group: FormGroup) {
    const debut = group.get('date_debut')?.value;
    const fin = group.get('date_fin')?.value;
    if (debut && fin && new Date(fin) < new Date(debut)) {
      return { dateFinInvalide: true };
    }
    return null;
  }

  chargerAbsences() {
    this.absenceService.getAll().subscribe((res: any) => {
      this.absences = res;
    });
  }

  onSubmit() {
    if (this.absenceForm.invalid) return;
    const data = this.absenceForm.value;

    // Conversion des dates au format YYYY-MM-DD
    if (data.date_debut instanceof Date) {
      const year = data.date_debut.getFullYear();
      const month = String(data.date_debut.getMonth() + 1).padStart(2, '0');
      const day = String(data.date_debut.getDate()).padStart(2, '0');
      data.date_debut = `${year}-${month}-${day}`;
    }
    if (data.date_fin instanceof Date) {
      const year = data.date_fin.getFullYear();
      const month = String(data.date_fin.getMonth() + 1).padStart(2, '0');
      const day = String(data.date_fin.getDate()).padStart(2, '0');
      data.date_fin = `${year}-${month}-${day}`;
    }

    if (this.absenceEnEdition) {
      this.absenceService.update(this.absenceEnEdition.id, data).subscribe(() => {
        this.snackBar.open('Absence modifiée avec succès', '', { duration: 2000 });
        this.absenceEnEdition = null;
        this.absenceForm.reset();
        this.chargerAbsences();
      });
    } else {
      this.absenceService.create(data).subscribe(() => {
        this.snackBar.open('Absence ajoutée avec succès', '', { duration: 2000 });
        this.absenceForm.reset();
        this.chargerAbsences();
      });
    }
  }

  editerAbsence(absence: any) {
    this.absenceEnEdition = absence;
    this.absenceForm.patchValue(absence);
  }

  annulerEdition() {
    this.absenceEnEdition = null;
    this.absenceForm.reset();
  }

  supprimerAbsence(id: number) {
    if (confirm('Voulez-vous vraiment supprimer cette absence ?')) {
      this.absenceService.delete(id).subscribe(() => {
        this.snackBar.open('Absence supprimée', '', { duration: 2000 });
        this.chargerAbsences();
      });
    }
  }
} 