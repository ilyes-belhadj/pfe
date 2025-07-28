import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';
import { MatSnackBar, MatSnackBarModule } from '@angular/material/snack-bar';
import { MatDialog, MatDialogModule } from '@angular/material/dialog';
import { PointageService } from 'src/app/services/pointage/pointage.service';
import { MaterialModule } from '../../material.module';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { EmployesComponent } from '../employe/employe-page.component';
import { MatNativeDateModule } from '@angular/material/core';
import { EmployeService } from 'src/app/services/employe/employe.service';

@Component({
  selector: 'app-pointage-page',
  templateUrl: './pointage-page.component.html',
  styleUrls: ['./pointage-page.component.scss'],
  standalone: true,
  imports: [
    CommonModule,
    FormsModule,
    ReactiveFormsModule,
    MaterialModule,
    MatSnackBarModule,
    MatDialogModule,
    MatNativeDateModule,
  ],
})
export class PointagePageComponent implements OnInit {
  pointages: any[] = [];
  pointageForm: FormGroup;
  pointageEnEdition: any = null;
  displayedColumns = ['employe', 'date', 'heure_entree', 'heure_sortie'];
  employes: any[] = [];

  constructor(
    private fb: FormBuilder,
    private pointageService: PointageService,
    private snackBar: MatSnackBar,
    private dialog: MatDialog,
    private employeService: EmployeService
  ) {
    this.pointageForm = this.fb.group({
      employe_id: ['', Validators.required],
      date_pointage: ['', Validators.required],
      heure_entree: ['', Validators.required],
      heure_sortie: ['', Validators.required],
    });
  }

  ngOnInit() {
    const today = new Date();
    this.pointageForm.get('date_pointage')?.setValue(today);
    this.chargerPointages();
    this.employeService.getAll().subscribe((res: any) => {
      this.employes = res;
    });
  }

  chargerPointages() {
    this.pointageService.getAll().subscribe((res: any) => {
      this.pointages = res.data; // ← Utilise la clé data qui contient le tableau
    });
  }

  onSubmit() {
    if (this.pointageForm.invalid) return;
    let data = this.pointageForm.value;

    // Conversion de la date au format YYYY-MM-DD
    if (data.date_pointage instanceof Date) {
      const year = data.date_pointage.getFullYear();
      const month = String(data.date_pointage.getMonth() + 1).padStart(2, '0');
      const day = String(data.date_pointage.getDate()).padStart(2, '0');
      data.date_pointage = `${year}-${month}-${day}`;
    }

    if (this.pointageEnEdition) {
      this.pointageService.update(this.pointageEnEdition.id, data).subscribe(() => {
        this.snackBar.open('Pointage modifié avec succès', '', { duration: 2000 });
        this.annulerEdition();
        this.chargerPointages();
      });
    } else {
      this.pointageService.create(data).subscribe(() => {
        this.snackBar.open('Pointage ajouté avec succès', '', { duration: 2000 });
        this.pointageForm.reset();
        this.pointageForm.get('date_pointage')?.setValue(new Date());
        this.chargerPointages();
      });
    }
  }

  editerPointage(pointage: any) {
    this.pointageEnEdition = pointage;
    this.pointageForm.patchValue(pointage);
  }

  annulerEdition() {
    this.pointageEnEdition = null;
    this.pointageForm.reset();
    this.pointageForm.get('date_pointage')?.setValue(new Date());
  }

  confirmerSuppression(pointage: any) {
    if (confirm('Voulez-vous vraiment supprimer ce pointage ?')) {
      this.supprimerPointage(pointage.id);
    }
  }

  supprimerPointage(id: number) {
    this.pointageService.delete(id).subscribe(() => {
      this.snackBar.open('Pointage supprimé', '', { duration: 2000 });
      this.chargerPointages();
    });
  }

  setHeureArriveeNow() {
    const now = new Date();
    const heure = now.toTimeString().slice(0, 8); // "HH:MM:SS"
    this.pointageForm.get('heure_entree')?.setValue(heure);
  }

  setHeureDepartNow() {
    const now = new Date();
    const heure = now.toTimeString().slice(0, 8); // "HH:MM:SS"
    this.pointageForm.get('heure_sortie')?.setValue(heure);
  }
} 