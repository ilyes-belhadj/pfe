import { Component } from '@angular/core';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';
import { AuthService } from 'src/app/services/auth/auth.service';
import { MatSnackBar, MatSnackBarModule } from '@angular/material/snack-bar';
import { MatCard, MatCardTitle, MatCardContent, MatCardActions } from '@angular/material/card';
import { MatFormField, MatError } from '@angular/material/form-field';
import { MatInput } from '@angular/material/input';
import { MatButton } from '@angular/material/button';
import { CommonModule } from '@angular/common';
import { Router } from '@angular/router';

@Component({
  selector: 'app-side-login',
  template: `
    <form [formGroup]="loginForm" (ngSubmit)="onSubmit()" style="max-width:400px;margin:auto;margin-top:3rem;">
      <mat-card>
        <mat-card-title>Connexion</mat-card-title>
        <mat-card-content>
          <mat-form-field appearance="outline" class="w-100">
            <label>Email</label>
            <input matInput formControlName="email" type="email" required>
            <mat-error *ngIf="loginForm.get('email')?.hasError('required')">Email requis</mat-error>
            <mat-error *ngIf="loginForm.get('email')?.hasError('email')">Format email invalide</mat-error>
          </mat-form-field>
          <mat-form-field appearance="outline" class="w-100">
            <label>Mot de passe</label>
            <input matInput formControlName="password" type="password" required>
            <mat-error *ngIf="loginForm.get('password')?.hasError('required')">Mot de passe requis</mat-error>
            <mat-error *ngIf="loginForm.get('password')?.hasError('minlength')">6 caractères minimum</mat-error>
          </mat-form-field>
        </mat-card-content>
        <mat-card-actions>
          <button mat-raised-button color="primary" type="submit" [disabled]="loginForm.invalid || loading">
            Se connecter
          </button>
        </mat-card-actions>
      </mat-card>
    </form>
  `,
  standalone: true,
  imports: [
    CommonModule,
    ReactiveFormsModule,
    MatCard,
    MatCardTitle,
    MatCardContent,
    MatCardActions,
    MatFormField,
    MatInput,
    MatButton,
    MatError,
    MatSnackBarModule,
  ],
})
export class SideLoginComponent {
  loginForm: FormGroup;
  loading = false;

  constructor(
    private fb: FormBuilder,
    private authService: AuthService,
    private snackBar: MatSnackBar,
    private router: Router
  ) {
    this.loginForm = this.fb.group({
      email: ['', [Validators.required, Validators.email]],
      password: ['', [Validators.required, Validators.minLength(6)]],
    });
  }

  onSubmit() {
    if (this.loginForm.invalid) return;
    this.loading = true;
    const { email, password } = this.loginForm.value;
    this.authService.login(email, password).subscribe({
      next: (res) => {
        this.loading = false;
        this.snackBar.open('Connexion réussie !', '', { duration: 2000 });

        // Stocke l'utilisateur et le token
        localStorage.setItem('user', JSON.stringify(res.user));
        localStorage.setItem('token', res.access_token);

        // Contrôle d'accès selon le rôle
        if (res.user.role_id === 2) {
          this.router.navigate(['/dashboard']); // admin
        } else if (res.user.role_id === 1) {
          this.router.navigate(['/dashboard/pointages']); // user
        } else {
          this.router.navigate(['/unauthorized']); // autre cas
        }
      },
      error: (err) => {
        this.loading = false;
        this.snackBar.open('Erreur de connexion : ' + (err?.error?.message || 'Vérifie tes identifiants'), '', { duration: 3000 });
      }
    });
  }
}