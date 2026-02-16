<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Vite;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Garantir que o symlink de storage exista em ambientes locais / docker
        // Sem o link `public/storage` os logos (e outros uploads) não carregam.
        $autoLink = env('CREATE_STORAGE_LINK', false);
        if ($autoLink && $this->app->environment(['local', 'development']) && ! is_link(public_path('storage'))) {
            try {
                \Illuminate\Support\Facades\Artisan::call('storage:link');
            } catch (\Throwable $e) {
                Log::warning('Falha ao criar storage:link automaticamente: '.$e->getMessage());
            }
        }

        // Forçar HTTPS apenas quando apropriado (produção ou quando explicitamente habilitado)
        // Em desenvolvimento/local, forçar HTTPS pode quebrar o server embutido do PHP/Artisan (sem TLS)
        try {
            $shouldForceHttps = $this->app->environment('production') || (bool) env('FORCE_HTTPS', false);
            if ($shouldForceHttps) {
                URL::forceScheme('https');
            }
        } catch (\Throwable $e) {
            Log::warning('Falha ao ajustar esquema https: '.$e->getMessage());
        }

        // Em produção, proibir comandos destrutivos do Artisan que poderiam apagar dados existentes
        // Proíbe: db:wipe, migrate:fresh, migrate:refresh, migrate:reset, migrate:rollback
        if ($this->app->environment('production')) {
            try {
                DB::prohibitDestructiveCommands();
            } catch (\Throwable $e) {
                Log::warning('Falha ao ativar proteção contra comandos destrutivos: '.$e->getMessage());
            }
        }

        // Override robusto dos caminhos de assets do Vite:
        // Gera URLs absolutas "/build/..." e ignora ASSET_URL incorreto que possa adicionar subcaminhos.
        // Seguro também em modo hot, pois o Vite ignora assetPath quando isRunningHot().
        try {
            app(Vite::class)
                ->useBuildDirectory('build')
                // Evitar que o Laravel detecte modo hot via public/hot em producao:
                // aponta o hot file para um local controlado que nao existe.
                ->useHotFile(storage_path('framework/vite.hot'))
                ->createAssetPathsUsing(function (string $path, ?bool $secure = null): string {
                    return '/'.ltrim($path, '/');
                });
        } catch (\Throwable $e) {
            Log::warning('Falha ao ajustar Vite asset paths: '.$e->getMessage());
        }

        // Fallback: garantir que o usuário admin tenha role super_admin em produção
        // Útil em plataformas sem shell (Render free), idempotente e leve.
        if ($this->app->environment('production')) {
            try {
                $email = env('DOCKER_ADMIN_EMAIL') ?? env('ADMIN_EMAIL', 'admin@example.com');
                /** @var \App\Models\User|null $admin */
                $admin = \App\Models\User::where('email', $email)->first();
                if ($admin && method_exists($admin, 'hasRole') && ! ($admin->hasRole('super_admin') || $admin->hasRole('super-admin'))) {
                    // Cria a role se não existir e atribui
                    $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'super_admin']);
                    $admin->assignRole($role);
                }
            } catch (\Throwable $e) {
                // Silencioso para não quebrar boot; loga se necessário
                Log::warning('Falha no fallback de super_admin: '.$e->getMessage());
            }
        }
    }
}
