<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Role;

// Créer l'utilisateur admin
$user = User::updateOrCreate(
    ['email' => 'admin@example.com'],
    [
        'name' => 'Admin',
        'password' => bcrypt('password'),
        'active' => true
    ]
);

// Assigner le rôle admin
$role = Role::where('name', 'Directeur')->first();
if ($role) {
    $user->role()->associate($role);
    $user->save();
}

echo "Utilisateur admin créé avec succès :\n";
echo "Email: admin@example.com\n";
echo "Mot de passe: password\n"; 