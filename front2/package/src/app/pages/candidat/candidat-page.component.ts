import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';
import { MatSnackBar, MatSnackBarModule } from '@angular/material/snack-bar';
import { MatDialog, MatDialogModule } from '@angular/material/dialog';
import { CandidatService } from 'src/app/services/candidat/candidat.service';
import { MaterialModule } from '../../material.module';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

@Component({
  selector: 'app-candidat-page',
  templateUrl: './candidat-page.component.html',
  styleUrls: ['./candidat-page.component.scss'],
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
export class CandidatPageComponent implements OnInit {
  candidats: any[] = [];
  candidatForm: FormGroup;
  candidatEnEdition: any = null;
  displayedColumns = ['nom', 'prenom', 'email', 'statut', 'actions'];

  constructor(
    private fb: FormBuilder,
    private candidatService: CandidatService,
    private snackBar: MatSnackBar,
    private dialog: MatDialog
  ) {
    this.candidatForm = this.fb.group({
      nom: ['', [Validators.required, Validators.minLength(2)]],
      prenom: ['', [Validators.required, Validators.minLength(2)]],
      email: ['', [Validators.required, Validators.email]],
      statut: ['', Validators.required],
      // Ajoute ici les autres champs nécessaires
    });
  }

  ngOnInit() {
    this.chargerCandidats();
  }

  chargerCandidats() {
    this.candidatService.getAll().subscribe(res => {
      this.candidats = Array.isArray(res) ? res : res.data;
    });
  }

  onSubmit() {
    if (this.candidatForm.invalid) return;
    const data = this.candidatForm.value;
    if (this.candidatEnEdition) {
      this.candidatService.update(this.candidatEnEdition.id, data).subscribe(() => {
        this.snackBar.open('Candidat modifié avec succès', '', { duration: 2000 });
        this.annulerEdition();
        this.chargerCandidats();
      });
    } else {
      this.candidatService.create(data).subscribe(() => {
        this.snackBar.open('Candidat ajouté avec succès', '', { duration: 2000 });
        this.candidatForm.reset();
        this.chargerCandidats();
      });
    }
  }

  editerCandidat(candidat: any) {
    this.candidatEnEdition = candidat;
    this.candidatForm.patchValue(candidat);
  }

  annulerEdition() {
    this.candidatEnEdition = null;
    this.candidatForm.reset();
  }

  confirmerSuppression(candidat: any) {
    if (confirm('Voulez-vous vraiment supprimer ce candidat ?')) {
      this.supprimerCandidat(candidat.id);
    }
  }

  supprimerCandidat(id: number) {
    this.candidatService.delete(id).subscribe(() => {
      this.snackBar.open('Candidat supprimé', '', { duration: 2000 });
      this.chargerCandidats();
    });
  }
} 