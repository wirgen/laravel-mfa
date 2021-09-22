<?php

declare(strict_types=1);

namespace Wirgen\LaravelMfa;

use Illuminate\Support\Carbon;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $now = Carbon::now();

            $migrations = [
                'create_user_mfa_table',
                'create_mfa_otp_table',
                'create_mfa_totp_table',
            ];
            foreach ($migrations as $migration) {
                foreach (glob(database_path("migrations/*.php")) as $filename) {
                    if ((substr($filename, -(mb_strlen($migration) + 4)) === $migration . '.php')) {
                        continue 2;
                    }
                }

                $this->publishes([
                    __DIR__ . "/../database/migrations/$migration.php" =>
                        database_path(
                            'migrations/' . $now->addSecond()->format('Y_m_d_His') . '_' . $migration . '.php'
                        ),
                ], 'mfa-migrations');
            }

            $this->publishes([
                __DIR__ . '/../config/mfa.php' => config_path('mfa.php'),
                __DIR__ . '/../config/mfa_otp.php' => config_path('mfa_otp.php'),
                __DIR__ . '/../config/mfa_totp.php' => config_path('mfa_totp.php'),
            ], 'mfa-config');
        }
    }

    public function register(): void
    {
        if (!app()->configurationIsCached()) {
            $this->mergeConfigFrom(__DIR__ . '/../config/mfa.php', 'mfa');
        }
    }
}
