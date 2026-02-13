<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Ordem importa:
        // 1) Criar todas as permissões e papéis da plataforma
        // 2) Criar/atualizar o Super Admin com papéis e senha fixa
        // (Opcional) 3) Popular dados de organização/usuários de teste em ambientes locais

        $this->call([
            PlatformRolesAndPermissionsSeeder::class,
            DockerSuperAdminSeeder::class,
        ]);

        if (app()->environment(['local', 'development'])) {
            // Dados de exemplo úteis para testes locais
            // Comentado se não desejar criar usuários/organizações de exemplo
            // $this->call([OrganizationDataSeeder::class, TestDataSeeder::class]);
        }
    }
}
