# Maison Confiance CRM Backend

Backend API Laravel pour le système de gestion CRM de Maison Confiance.

## 🚀 Fonctionnalités

- **Authentification JWT** : Système d'authentification sécurisé avec tokens JWT
- **Gestion des utilisateurs** : CRUD complet pour les utilisateurs avec rôles
- **Gestion des employés** : Suivi des employés avec départements
- **Gestion des absences** : Suivi des congés et absences des employés
- **Gestion des projets** : Suivi des projets avec statuts
- **Gestion des départements** : Organisation des équipes par département

## 📋 Prérequis

- PHP 8.2+
- Composer
- MySQL/PostgreSQL
- Node.js (pour les assets frontend)

## 🛠️ Installation

1. **Cloner le projet**
   ```bash
   git clone [url-du-repo]
   cd maison-confiance-CRM-backend
   ```

2. **Installer les dépendances**
   ```bash
   composer install
   ```

3. **Configuration de l'environnement**
   ```bash
   cp .env.example .env
   php artisan key:generate
   php artisan jwt:secret
   ```

4. **Configuration de la base de données**
   - Créer une base de données MySQL
   - Configurer les variables DB_* dans le fichier .env
   - Exécuter les migrations :
   ```bash
   php artisan migrate
   ```

5. **Seeder les données**
   ```bash
   php artisan db:seed
   ```

6. **Démarrer le serveur**
   ```bash
   php artisan serve
   ```

## 🔐 Authentification

L'API utilise JWT pour l'authentification. Les endpoints protégés nécessitent un token Bearer dans le header Authorization.

### Endpoints d'authentification :
- `POST /api/login` - Connexion
- `POST /api/register` - Inscription
- `GET /api/auth/me` - Profil utilisateur
- `POST /api/auth/logout` - Déconnexion
- `POST /api/auth/refresh` - Rafraîchir le token

## 📚 API Endpoints

### Utilisateurs
- `GET /api/auth/users` - Liste des utilisateurs
- `POST /api/auth/users` - Créer un utilisateur
- `GET /api/auth/users/{id}` - Détails d'un utilisateur
- `PUT /api/auth/users/{id}` - Modifier un utilisateur
- `DELETE /api/auth/users/{id}` - Supprimer un utilisateur

### Employés
- `GET /api/auth/employes` - Liste des employés
- `POST /api/auth/employes` - Créer un employé
- `GET /api/auth/employes/{id}` - Détails d'un employé
- `PUT /api/auth/employes/{id}` - Modifier un employé
- `DELETE /api/auth/employes/{id}` - Supprimer un employé

### Absences
- `GET /api/auth/absences` - Liste des absences
- `POST /api/auth/absences` - Créer une absence
- `GET /api/auth/absences/{id}` - Détails d'une absence
- `PUT /api/auth/absences/{id}` - Modifier une absence
- `DELETE /api/auth/absences/{id}` - Supprimer une absence

### Départements
- `GET /api/auth/departements` - Liste des départements
- `POST /api/auth/departements` - Créer un département
- `GET /api/auth/departements/{id}` - Détails d'un département
- `PUT /api/auth/departements/{id}` - Modifier un département
- `DELETE /api/auth/departements/{id}` - Supprimer un département

### Projets
- `GET /api/auth/projects` - Liste des projets
- `POST /api/auth/projects` - Créer un projet
- `GET /api/auth/projects/{id}` - Détails d'un projet
- `PUT /api/auth/projects/{id}` - Modifier un projet
- `DELETE /api/auth/projects/{id}` - Supprimer un projet

## 🧪 Tests

```bash
php artisan test
```

## 📝 Structure du projet

```
app/
├── Http/
│   ├── Controllers/Api/     # Contrôleurs API
│   ├── Middleware/          # Middleware personnalisés
│   ├── Requests/            # Validation des requêtes
│   └── Resources/           # Ressources API
├── Models/                  # Modèles Eloquent
└── Providers/              # Service providers

database/
├── migrations/             # Migrations de base de données
└── seeders/               # Seeders pour les données de test

routes/
└── api.php                # Routes API

tests/                     # Tests automatisés
```

## 🤝 Contribution

1. Fork le projet
2. Créer une branche feature (`git checkout -b feature/AmazingFeature`)
3. Commit les changements (`git commit -m 'Add some AmazingFeature'`)
4. Push vers la branche (`git push origin feature/AmazingFeature`)
5. Ouvrir une Pull Request

## 📄 Licence

Ce projet est sous licence MIT. Voir le fichier `LICENSE` pour plus de détails.

## 🆘 Support

Pour toute question ou problème, veuillez ouvrir une issue sur GitHub.
