<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Blade;use Illuminate\Support\Facades\Gate;

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

      Blade::directive('role', function ($expression) {
        return "<?php if (auth()->user()->hasRole({$expression})) : ?>";
      });

      Blade::directive('endrole', function ($expression) {
        return "<?php endif; ?>";
      });

      Blade::directive('permission', function ($expression) {
        return "<?php if (auth()->user()->hasPermissionTo({$expression})) : ?>";
      });

      Blade::directive('endpermission', function ($expression) {
        return "<?php endif; ?>";
      });

      Gate::define('manage-session', function (User $user, $session) {
        if ($user->hasPermission('users.manage')) {
          return true;
        }

        return (int) $user->id === (int) $session->user_id;
      });
    }
}
