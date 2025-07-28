import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';
import { MatSnackBar, MatSnackBarModule } from '@angular/material/snack-bar';
import { MatDialog, MatDialogModule } from '@angular/material/dialog';
import { EvaluationService } from 'src/app/services/evaluation/evaluation.service';
import { MaterialModule } from '../../material.module';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

@Component({
  selector: 'app-evaluation-page',
  templateUrl: './evaluation-page.component.html',
  styleUrls: ['./evaluation-page.component.scss'],
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
export class EvaluationPageComponent implements OnInit {
  evaluations: any[] = [];
  evaluationForm: FormGroup;
  evaluationEnEdition: any = null;
  displayedColumns = ['titre', 'type', 'statut', 'date_evaluation', 'actions'];
  types = ['candidat', 'employe', 'periode_essai', 'annuelle', 'performance'];
  statuts = ['brouillon', 'en_cours', 'terminee', 'validee', 'rejetee'];

  constructor(
    private fb: FormBuilder,
    private evaluationService: EvaluationService,
    private snackBar: MatSnackBar,
    private dialog: MatDialog
  ) {
    this.evaluationForm = this.fb.group({
      titre: ['', Validators.required],
      type: ['', Validators.required],
      statut: ['', Validators.required],
      date_evaluation: [new Date().toISOString().substring(0, 10), Validators.required],
      evaluateur_id: [1, Validators.required], // À remplacer par l'ID de l'utilisateur connecté si besoin
      evaluable_type: ['App\\Models\\Employe', Validators.required],
      evaluable_id: [1, Validators.required], // ID de l'entité évaluée (employé ou candidat)
    });
  }

  ngOnInit() {
    this.chargerEvaluations();
  }

  chargerEvaluations() {
    this.evaluationService.getAll().subscribe(evaluations => {
      console.log('Réponse API évaluations:', evaluations);
      this.evaluations = evaluations.data ? evaluations.data : evaluations;
      console.log('Évaluations après traitement:', this.evaluations);
    });
  }

  onSubmit() {
    if (this.evaluationForm.invalid) return;
    
    const data = this.evaluationForm.value;
    
    // Convertir la date au format datetime pour Laravel
    if (data.date_evaluation) {
      data.date_evaluation = data.date_evaluation + ' 00:00:00';
    }
    
    // S'assurer que les champs obligatoires sont présents
    if (!data.evaluable_type) {
      data.evaluable_type = 'App\\Models\\Employe';
    }
    if (!data.evaluable_id) {
      data.evaluable_id = 1;
    }
    
    console.log('=== DONNÉES COMPLÈTES ===');
    console.log('Données envoyées à l\'API:', JSON.stringify(data, null, 2));
    console.log('evaluable_type:', data.evaluable_type);
    console.log('evaluable_id:', data.evaluable_id);
    console.log('Toutes les clés:', Object.keys(data));
    console.log('========================');
    
    if (this.evaluationEnEdition) {
      this.evaluationService.update(this.evaluationEnEdition.id, data).subscribe({
        next: () => {
          this.snackBar.open('Évaluation modifiée avec succès', '', { duration: 2000 });
          this.annulerEdition();
          this.chargerEvaluations();
        },
        error: (err) => {
          this.snackBar.open('Erreur lors de la modification : ' + (err?.error?.message || 'Erreur inconnue'), '', { duration: 3000 });
          console.error('Erreur API:', err);
        }
      });
    } else {
      this.evaluationService.create(data).subscribe({
        next: () => {
          this.snackBar.open('Évaluation ajoutée avec succès', '', { duration: 2000 });
          this.evaluationForm.reset({
            titre: '',
            type: '',
            statut: '',
            date_evaluation: new Date().toISOString().substring(0, 10),
            evaluateur_id: 1,
            evaluable_type: 'App\\Models\\Employe',
            evaluable_id: 1
          });
          this.chargerEvaluations();
        },
        error: (err) => {
          this.snackBar.open('Erreur lors de l\'ajout : ' + (err?.error?.message || 'Erreur inconnue'), '', { duration: 3000 });
          console.error('Erreur API:', err);
        }
      });
    }
  }

  editerEvaluation(evaluation: any) {
    this.evaluationEnEdition = evaluation;
    this.evaluationForm.patchValue(evaluation);
  }

  annulerEdition() {
    this.evaluationEnEdition = null;
    this.evaluationForm.reset({
      titre: '',
      type: '',
      statut: '',
      date_evaluation: new Date().toISOString().substring(0, 10),
      evaluateur_id: 1,
      evaluable_type: 'App\\Models\\Employe',
      evaluable_id: 1
    });
  }

  confirmerSuppression(evaluation: any) {
    // if (window.confirm('Voulez-vous vraiment supprimer cette évaluation ?')) {
      this.supprimerEvaluation(evaluation.id);
    // }
  }

  supprimerEvaluation(id: number) {
    this.evaluationService.delete(id).subscribe(() => {
      this.snackBar.open('Évaluation supprimée', '', { duration: 2000 });
      this.chargerEvaluations();
    });
  }
} 