import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';
import { MatSnackBar, MatSnackBarModule } from '@angular/material/snack-bar';
import { MatDialog, MatDialogModule } from '@angular/material/dialog';
import { PaieService } from 'src/app/services/paie/paie.service';
import { MaterialModule } from '../../material.module';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

@Component({
  selector: 'app-paie-page',
  templateUrl: './paie-page.component.html',
  styleUrls: ['./paie-page.component.scss'],
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
export class PaiePageComponent implements OnInit {
  paies: any[] = [];
  paieForm: FormGroup;
  paieEnEdition: any = null;
  displayedColumns = ['employe', 'mois', 'salaire', 'statut', 'actions'];

  constructor(
    private fb: FormBuilder,
    private paieService: PaieService,
    private snackBar: MatSnackBar,
    private dialog: MatDialog
  ) {
    this.paieForm = this.fb.group({
      employe_id: ['', Validators.required],
      mois: ['', Validators.required],
      salaire: ['', [Validators.required, Validators.min(0)]],
      statut: ['', Validators.required],
      // Ajoute ici les autres champs nécessaires
    });
  }

  ngOnInit() {
    this.chargerPaies();
  }

  chargerPaies() {
    this.paieService.getAll().subscribe(paies => this.paies = paies);
  }

  onSubmit() {
    if (this.paieForm.invalid) return;
    const data = this.paieForm.value;
    if (this.paieEnEdition) {
      this.paieService.update(this.paieEnEdition.id, data).subscribe(() => {
        this.snackBar.open('Paie modifiée avec succès', '', { duration: 2000 });
        this.annulerEdition();
        this.chargerPaies();
      });
    } else {
      this.paieService.create(data).subscribe(() => {
        this.snackBar.open('Paie ajoutée avec succès', '', { duration: 2000 });
        this.paieForm.reset();
        this.chargerPaies();
      });
    }
  }

  editerPaie(paie: any) {
    this.paieEnEdition = paie;
    this.paieForm.patchValue(paie);
  }

  annulerEdition() {
    this.paieEnEdition = null;
    this.paieForm.reset();
  }

  confirmerSuppression(paie: any) {
    if (confirm('Voulez-vous vraiment supprimer cette paie ?')) {
      this.supprimerPaie(paie.id);
    }
  }

  supprimerPaie(id: number) {
    this.paieService.delete(id).subscribe(() => {
      this.snackBar.open('Paie supprimée', '', { duration: 2000 });
      this.chargerPaies();
    });
  }
} 