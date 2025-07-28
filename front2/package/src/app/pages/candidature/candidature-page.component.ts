import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';
import { MatSnackBar, MatSnackBarModule } from '@angular/material/snack-bar';
import { MatDialog, MatDialogModule } from '@angular/material/dialog';
import { CandidatureService } from 'src/app/services/candidature/candidature.service';
import { MaterialModule } from '../../material.module';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { CandidatService } from 'src/app/services/candidat/candidat.service';
import { DepartementService } from 'src/app/services/departement/departement.service';
import { UserService } from 'src/app/services/user/user.service';
import { MatNativeDateModule } from '@angular/material/core';

@Component({
  selector: 'app-candidature-page',
  templateUrl: './candidature-page.component.html',
  styleUrls: ['./candidature-page.component.scss'],
  standalone: true,
  imports: [
    CommonModule,
    FormsModule,
    ReactiveFormsModule,
    MaterialModule,
    MatNativeDateModule,
    MatSnackBarModule,
    MatDialogModule,
  ],
})
export class CandidaturePageComponent implements OnInit {
  candidatures: any[] = [];
  candidatureForm: FormGroup;
  candidatureEnEdition: any = null;
  displayedColumns = ['statut', 'date_candidature', 'actions'];
  candidats: any[] = [];
  departements: any[] = [];
  recruteurs: any[] = [];
  selectedCvFile: File | null = null;
  selectedLettreFile: File | null = null;

  constructor(
    private fb: FormBuilder,
    private candidatService: CandidatService,
    private departementService: DepartementService,
    private userService: UserService,
    private candidatureService: CandidatureService,
    private snackBar: MatSnackBar,
    private dialog: MatDialog
  ) {
    this.candidatureForm = this.fb.group({
      candidat_id: ['', Validators.required],
      departement_id: ['', Validators.required],
      recruteur_id: ['', Validators.required],
      statut: ['', Validators.required],
      date_candidature: ['', Validators.required],
    });
  }

  ngOnInit() {
    this.chargerCandidatures();
    this.candidatService.getAll().subscribe(res => this.candidats = Array.isArray(res) ? res : res.data);
    this.departementService.getAll().subscribe(res => this.departements = Array.isArray(res) ? res : res.data);
    this.userService.getAll().subscribe(res => this.recruteurs = Array.isArray(res) ? res : res.data);
  }

  chargerCandidatures() {
    this.candidatureService.getAll().subscribe(candidatures => this.candidatures = candidatures);
  }

  onCvSelected(event: any) {
    this.selectedCvFile = event.target.files[0];
  }

  onLettreSelected(event: any) {
    this.selectedLettreFile = event.target.files[0];
  }

  onSubmit() {
    if (this.candidatureForm.invalid) return;
    const data = { ...this.candidatureForm.value };
    // Conversion de la date au format YYYY-MM-DD
    if (data.date_candidature instanceof Date) {
      const year = data.date_candidature.getFullYear();
      const month = String(data.date_candidature.getMonth() + 1).padStart(2, '0');
      const day = String(data.date_candidature.getDate()).padStart(2, '0');
      data.date_candidature = `${year}-${month}-${day}`;
    }
    const formData = new FormData();
    Object.keys(data).forEach(key => formData.append(key, data[key]));
    if (this.selectedCvFile) formData.append('cv', this.selectedCvFile);
    if (this.selectedLettreFile) formData.append('lettre_motivation', this.selectedLettreFile);
    if (this.candidatureEnEdition) {
      this.candidatureService.update(this.candidatureEnEdition.id, formData).subscribe(() => {
        this.snackBar.open('Candidature modifiée avec succès', '', { duration: 2000 });
        this.annulerEdition();
        this.chargerCandidatures();
      });
    } else {
      this.candidatureService.create(formData).subscribe(() => {
        this.snackBar.open('Candidature ajoutée avec succès', '', { duration: 2000 });
        this.candidatureForm.reset();
        this.selectedCvFile = null;
        this.selectedLettreFile = null;
        this.chargerCandidatures();
      });
    }
  }

  editerCandidature(candidature: any) {
    this.candidatureEnEdition = candidature;
    this.candidatureForm.patchValue(candidature);
  }

  annulerEdition() {
    this.candidatureEnEdition = null;
    this.candidatureForm.reset();
  }

  confirmerSuppression(candidature: any) {
    if (confirm('Voulez-vous vraiment supprimer cette candidature ?')) {
      this.supprimerCandidature(candidature.id);
    }
  }

  supprimerCandidature(id: number) {
    this.candidatureService.delete(id).subscribe(() => {
      this.snackBar.open('Candidature supprimée', '', { duration: 2000 });
      this.chargerCandidatures();
    });
  }
} 