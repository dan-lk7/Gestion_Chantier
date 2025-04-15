<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $roles = ['Admin', 'Responsable', 'Auditeur'];

        foreach ($roles as $role) {
            Role::firstOrCreate(['nom' => $role]); // Vérifie s'il existe avant de l'insérer
        }
    }
}

