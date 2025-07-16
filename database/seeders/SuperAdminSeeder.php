<?php

namespace Database\Seeders;

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Vérifier si le Super Admin existe déjà
        $existingSuperAdmin = User::where('role', UserRole::SuperAdmin)->first();

        if (!$existingSuperAdmin) {
            User::create([
                'name' => 'Super Admin',
                'email' => 'admin@optisys.ma',
                'password' => Hash::make('password'), // Mot de passe par défaut
                'role' => UserRole::SuperAdmin,
                'tenant_id' => null, // Super Admin n'appartient à aucun tenant
            ]);

            $this->command->info('Super Administrateur créé avec succès !');
            $this->command->info('Email: admin@optisys.ma');
            $this->command->info('Mot de passe: password');
        } else {
            $this->command->info('Super Administrateur existe déjà.');
        }
    }
}
