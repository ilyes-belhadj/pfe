import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';
import { MatSnackBar, MatSnackBarModule } from '@angular/material/snack-bar';
import { MatDialog, MatDialogModule } from '@angular/material/dialog';
import { OffreEmploiService } from 'src/app/services/offre-emploi/offre-emploi.service';
import { MaterialModule } from '../../material.module';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

@Component({
  selector: 'app-offre-emploi-page',
  templateUrl: './offre-emploi-page.component.html',
  styleUrls: ['./offre-emploi-page.component.scss'],
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
export class OffreEmploiPageComponent implements OnInit {
  offresEmploi: any[] = [];
  offreForm: FormGroup;
  offreEmploiEnEdition: any = null;
  displayedColumns = ['titre', 'type_contrat', 'statut', 'date_publication', 'actions'];

  constructor(
    private fb: FormBuilder,
    private offreEmploiService: OffreEmploiService,
    private snackBar: MatSnackBar,
    private dialog: MatDialog
  ) {
    this.offreForm = this.fb.group({
      titre: ['', [Validators.required, Validators.minLength(2)]],
      type_contrat: ['', Validators.required],
      statut: ['', Validators.required],
      date_publication: ['', Validators.required],
      // Ajoute ici les autres champs nécessaires
    });
  }

  ngOnInit() {
    this.chargerOffresEmploi();
  }

  chargerOffresEmploi() {
    this.offreEmploiService.getAll().subscribe(offres => this.offresEmploi = offres);
  }

  onSubmit() {
    if (this.offreForm.invalid) return;
    const data = this.offreForm.value;
    if (this.offreEmploiEnEdition) {
      this.offreEmploiService.update(this.offreEmploiEnEdition.id, data).subscribe(() => {
        this.snackBar.open('Offre modifiée avec succès', '', { duration: 2000 });
        this.annulerEdition();
        this.chargerOffresEmploi();
      });
    } else {
      this.offreEmploiService.create(data).subscribe(() => {
        this.snackBar.open('Offre ajoutée avec succès', '', { duration: 2000 });
        this.offreForm.reset();
        this.chargerOffresEmploi();
      });
    }
  }

  editerOffreEmploi(offre: any) {
    this.offreEmploiEnEdition = offre;
    this.offreForm.patchValue(offre);
  }

  annulerEdition() {
    this.offreEmploiEnEdition = null;
    this.offreForm.reset();
  }

  confirmerSuppression(offre: any) {
    if (confirm('Voulez-vous vraiment supprimer cette offre ?')) {
      this.supprimerOffreEmploi(offre.id);
    }
  }

  supprimerOffreEmploi(id: number) {
    this.offreEmploiService.delete(id).subscribe(() => {
      this.snackBar.open('Offre supprimée', '', { duration: 2000 });
      this.chargerOffresEmploi();
    });
  }
} 