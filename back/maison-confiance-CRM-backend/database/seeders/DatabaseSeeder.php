<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
        
        // Assigner le rôle "Directeur" à l'utilisateur de test
        $role = \App\Models\Role::where('name', 'Directeur')->first();
        if ($role) {
            $user->role()->associate($role);
            $user->save();
        }

        // Créer l'utilisateur admin
        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => bcrypt('password'),
                'active' => true
            ]
        );

        // Assigner le rôle admin
        if ($role) {
            $admin->role()->associate($role);
            $admin->save();
        }
        
        $this->call([
            RoleSeeder::class,
            ProjectSeeder::class,
            DepartementSeeder::class,
            EmployeSeeder::class,
            AbsenceSeeder::class,
            FormationSeeder::class,
            PaieSeeder::class,
            PointageSeeder::class,
            CandidatSeeder::class,
            CandidatureSeeder::class,
            EvaluationSeeder::class,
            OffreEmploiSeeder::class,
        ]);
    }
}
