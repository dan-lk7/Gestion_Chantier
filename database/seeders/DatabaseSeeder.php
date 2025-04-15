<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Chantier;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Exécuter le seeder des rôles pour éviter les doublons
        $this->call(RoleSeeder::class);

        // Vérifier si un chantier existe déjà pour éviter les doublons
        $chantier = Chantier::firstOrCreate([
            'nom' => 'Chantier Principal',
            'ville' => 'Paris',
            'pays' => 'France',
        ]);

        // Récupérer les rôles créés par RoleSeeder
        $adminRole = Role::where('nom', 'Admin')->first();
        $responsableRole = Role::where('nom', 'Responsable')->first();
        $auditeurRole = Role::where('nom', 'Auditeur')->first();

        // Vérifier si les utilisateurs existent déjà pour éviter les doublons
        User::firstOrCreate([
            'email' => 'admin@gestion.com',
        ], [
            'name' => 'Admin User',
            'password' => Hash::make('password'),
            'role_id' => $adminRole->id,
            'chantier_id' => null,
        ]);

        User::firstOrCreate([
            'email' => 'responsable@chantier.com',
        ], [
            'name' => 'Responsable Chantier',
            'password' => Hash::make('password'),
            'role_id' => $responsableRole->id,
            'chantier_id' => $chantier->id,
        ]);

        User::firstOrCreate([
            'email' => 'auditeur@gestion.com',
        ], [
            'name' => 'Paul Auditeur',
            'password' => Hash::make('password'),
            'role_id' => $auditeurRole->id,
            'chantier_id' => null,
        ]);
    }
}
