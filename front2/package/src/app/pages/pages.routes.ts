import { Routes } from '@angular/router';
import { StarterComponent } from './starter/starter.component';
import { CandidatPageComponent } from './candidat/candidat-page.component';
import { CandidaturePageComponent } from './candidature/candidature-page.component';
import { DepartementPageComponent } from './departement/departement-page.component';
import { EmployesComponent } from './employe/employe-page.component';
import { EvaluationPageComponent } from './evaluation/evaluation-page.component';
import { FormationPageComponent } from './formation/formation-page.component';
import { OffreEmploiPageComponent } from './offre-emploi/offre-emploi-page.component';
import { PaiePageComponent } from './paie/paie-page.component';
import { PointagePageComponent } from './pointage/pointage-page.component';
import { ProjectPageComponent } from './project/project-page.component';
import { RolePageComponent } from './role/role-page.component';
import { UserPageComponent } from './user/user-page.component';
import { AbsencePageComponent } from './absence/absence-page.component';

export const PagesRoutes: Routes = [
  {
    path: '',
    component: StarterComponent,
    data: {
      title: 'Starter Page',
      urls: [
        { title: 'Dashboard', url: '/dashboards/dashboard1' },
        { title: 'Starter Page' },
      ],
    },
  },
  { path: 'candidats', component: CandidatPageComponent },
  { path: 'candidatures', component: CandidaturePageComponent },
  { path: 'departements', component: DepartementPageComponent },
  { path: 'employes', component: EmployesComponent },
  { path: 'evaluations', component: EvaluationPageComponent },
  { path: 'formations', component: FormationPageComponent },
  { path: 'offres-emploi', component: OffreEmploiPageComponent },
  { path: 'paies', component: PaiePageComponent },
  { path: 'pointages', component: PointagePageComponent },
  { path: 'projects', component: ProjectPageComponent },
  { path: 'roles', component: RolePageComponent },
  { path: 'users', component: UserPageComponent },
  { path: 'absences', component: AbsencePageComponent },
];
