import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';
import { MatSnackBar, MatSnackBarModule } from '@angular/material/snack-bar';
import { MatDialog, MatDialogModule } from '@angular/material/dialog';
import { ProjectService } from 'src/app/services/project/project.service';
import { MaterialModule } from '../../material.module';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

@Component({
  selector: 'app-project-page',
  templateUrl: './project-page.component.html',
  styleUrls: ['./project-page.component.scss'],
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
export class ProjectPageComponent implements OnInit {
  projects: any[] = [];
  projectForm: FormGroup;
  projectEnEdition: any = null;
  displayedColumns = ['nom', 'date_debut', 'date_fin', 'statut', 'actions'];

  constructor(
    private fb: FormBuilder,
    private projectService: ProjectService,
    private snackBar: MatSnackBar,
    private dialog: MatDialog
  ) {
    this.projectForm = this.fb.group({
      nom: ['', [Validators.required, Validators.minLength(2)]],
      date_debut: ['', Validators.required],
      date_fin: ['', Validators.required],
      statut: ['', Validators.required],
      // Ajoute ici les autres champs nécessaires
    });
  }

  ngOnInit() {
    this.chargerProjects();
  }

  chargerProjects() {
    this.projectService.getAll().subscribe(projects => this.projects = projects);
  }

  onSubmit() {
    if (this.projectForm.invalid) return;
    const data = this.projectForm.value;
    if (this.projectEnEdition) {
      this.projectService.update(this.projectEnEdition.id, data).subscribe(() => {
        this.snackBar.open('Projet modifié avec succès', '', { duration: 2000 });
        this.annulerEdition();
        this.chargerProjects();
      });
    } else {
      this.projectService.create(data).subscribe(() => {
        this.snackBar.open('Projet ajouté avec succès', '', { duration: 2000 });
        this.projectForm.reset();
        this.chargerProjects();
      });
    }
  }

  editerProject(project: any) {
    this.projectEnEdition = project;
    this.projectForm.patchValue(project);
  }

  annulerEdition() {
    this.projectEnEdition = null;
    this.projectForm.reset();
  }

  confirmerSuppression(project: any) {
    if (confirm('Voulez-vous vraiment supprimer ce projet ?')) {
      this.supprimerProject(project.id);
    }
  }

  supprimerProject(id: number) {
    this.projectService.delete(id).subscribe(() => {
      this.snackBar.open('Projet supprimé', '', { duration: 2000 });
      this.chargerProjects();
    });
  }
} 