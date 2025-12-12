<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Driver;
use App\Models\User;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

    Gate::define('driver-create', function (User $user) {
        return true;
    });

    // редактировать / мягко удалять – владелец или админ
    Gate::define('driver-update', function (User $user, Driver $driver) {
        return $user->id === $driver->user_id || $user->is_admin;
    });

    Gate::define('driver-delete', function (User $user, Driver $driver) {
        return $user->id === $driver->user_id || $user->is_admin;
    });

    // восстановить / удалить безвозвратно – только админ
    Gate::define('driver-restore', function (User $user, Driver $driver) {
        return $user->is_admin;
    });

    Gate::define('driver-force-delete', function (User $user, Driver $driver) {
        return $user->is_admin;
    });
    }
}
