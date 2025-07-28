import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';
import { MatSnackBar, MatSnackBarModule } from '@angular/material/snack-bar';
import { MatDialog, MatDialogModule } from '@angular/material/dialog';
import { FormationService } from 'src/app/services/formation/formation.service';
import { MaterialModule } from 'src/app/material.module';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

@Component({
  selector: 'app-formation-page',
  templateUrl: './formation-page.component.html',
  styleUrls: ['./formation-page.component.scss'],
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
export class FormationPageComponent implements OnInit {
  formations: any[] = [];
  formationForm: FormGroup;
  formationEnEdition: any = null;
  displayedColumns = ['titre', 'formateur', 'date_debut', 'statut', 'actions'];

  constructor(
    private fb: FormBuilder,
    private formationService: FormationService,
    private snackBar: MatSnackBar,
    private dialog: MatDialog
  ) {
    this.formationForm = this.fb.group({
      titre: ['', [Validators.required, Validators.minLength(2)]],
      formateur: ['', Validators.required],
      date_debut: ['', Validators.required],
      statut: ['', Validators.required],
      // Ajoute ici les autres champs nécessaires
    });
  }

  ngOnInit() {
    this.chargerFormations();
  }

  chargerFormations() {
    this.formationService.getAll().subscribe((formations: any[]) => this.formations = formations);
  }

  onSubmit() {
    if (this.formationForm.invalid) return;
    const data = this.formationForm.value;
    if (this.formationEnEdition) {
      this.formationService.update(this.formationEnEdition.id, data).subscribe(() => {
        this.snackBar.open('Formation modifiée avec succès', '', { duration: 2000 });
        this.annulerEdition();
        this.chargerFormations();
      });
    } else {
      this.formationService.create(data).subscribe(() => {
        this.snackBar.open('Formation ajoutée avec succès', '', { duration: 2000 });
        this.formationForm.reset();
        this.chargerFormations();
      });
    }
  }

  editerFormation(formation: any) {
    this.formationEnEdition = formation;
    this.formationForm.patchValue(formation);
  }

  annulerEdition() {
    this.formationEnEdition = null;
    this.formationForm.reset();
  }

  confirmerSuppression(formation: any) {
    if (confirm('Voulez-vous vraiment supprimer cette formation ?')) {
      this.supprimerFormation(formation.id);
    }
  }

  supprimerFormation(id: number) {
    this.formationService.delete(id).subscribe(() => {
      this.snackBar.open('Formation supprimée', '', { duration: 2000 });
      this.chargerFormations();
    });
  }
} 