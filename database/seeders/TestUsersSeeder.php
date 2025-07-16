<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Tenant;
use App\Enums\UserRole;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer les tenants existants
        $tenants = Tenant::all();

        if ($tenants->count() === 0) {
            $this->command->info('Aucun tenant trouvé. Créez d\'abord des tenants.');
            return;
        }

        foreach ($tenants as $tenant) {
            // Vérifier si un utilisateur admin existe déjà pour ce tenant
            $existingAdmin = User::where('tenant_id', $tenant->id)
                                ->where('role', UserRole::AdminMagasin)
                                ->first();

            if (!$existingAdmin) {
                // Créer un utilisateur admin pour ce tenant
                $user = User::create([
                    'name' => 'Admin ' . $tenant->name,
                    'email' => 'admin@' . strtolower(str_replace(' ', '', $tenant->name)) . '.com',
                    'password' => Hash::make('password'),
                    'role' => UserRole::AdminMagasin,
                    'tenant_id' => $tenant->id,
                ]);

                $this->command->info("Utilisateur créé pour {$tenant->name}: {$user->email} / password");
            } else {
                // Mettre à jour le mot de passe de l'utilisateur existant
                $existingAdmin->update([
                    'password' => Hash::make('password')
                ]);

                $this->command->info("Mot de passe mis à jour pour {$existingAdmin->email} / password");
            }
        }
    }
}
