import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';
import { MatSnackBar, MatSnackBarModule } from '@angular/material/snack-bar';
import { MatDialog, MatDialogModule } from '@angular/material/dialog';
import { DepartementService } from 'src/app/services/departement/departement.service';
import { MaterialModule } from '../../material.module';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

@Component({
  selector: 'app-departement-page',
  templateUrl: './departement-page.component.html',
  styleUrls: ['./departement-page.component.scss'],
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
export class DepartementPageComponent implements OnInit {
  departements: any[] = [];
  departementForm: FormGroup;
  departementEnEdition: any = null;
  displayedColumns = ['nom', 'description', 'actions'];

  constructor(
    private fb: FormBuilder,
    private departementService: DepartementService,
    private snackBar: MatSnackBar,
    private dialog: MatDialog
  ) {
    this.departementForm = this.fb.group({
      nom: ['', [Validators.required, Validators.minLength(2)]],
      description: [''],
    });
  }

  ngOnInit() {
    this.chargerDepartements();
  }

  chargerDepartements() {
    this.departementService.getAll().subscribe(departements => {
      this.departements = departements.data;
    });
  }

  onSubmit() {
    if (this.departementForm.invalid) return;
    const data = this.departementForm.value;
    console.log('Données envoyées à l\'API:', data);
    if (this.departementEnEdition) {
      this.departementService.update(this.departementEnEdition.id, data).subscribe(() => {
        this.snackBar.open('Département modifié avec succès', '', { duration: 2000 });
        this.annulerEdition();
        this.chargerDepartements();
      });
    } else {
      this.departementService.create(data).subscribe(() => {
        this.snackBar.open('Département ajouté avec succès', '', { duration: 2000 });
        this.departementForm.reset();
        this.chargerDepartements();
      });
    }
  }

  editerDepartement(departement: any) {
    this.departementEnEdition = departement;
    this.departementForm.patchValue(departement);
  }

  annulerEdition() {
    this.departementEnEdition = null;
    this.departementForm.reset();
  }

  confirmerSuppression(departement: any) {
    if (confirm('Voulez-vous vraiment supprimer ce département ?')) {
      this.supprimerDepartement(departement.id);
    }
  }

  supprimerDepartement(id: number) {
    this.departementService.delete(id).subscribe(() => {
      this.snackBar.open('Département supprimé', '', { duration: 2000 });
      this.chargerDepartements();
    });
  }
} 