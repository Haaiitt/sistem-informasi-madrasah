<?php

namespace App\Providers;


use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Actions\Fortify\UpdateUserPassword;

class FortifyServiceProvider extends ServiceProvider
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
        Fortify::loginView(fn () => view('auth.login'));

        // BR-19: login username + password. Username dicocokkan huruf kecil (D-18).
        Fortify::authenticateUsing(function (Request $request) {
            $user = User::where('username', strtolower((string) $request->username))->first();

            if (! $user || ! Hash::check($request->password, $user->password)) {
                return null;
            }

            // FR-AUTH-04: is_active = false berarti tidak dapat login.
            if (! $user->is_active) {
                return null;
            }

            $user->forceFill(['last_login_at' => now()])->save();

            return $user;
        });

        // architecture.md §5: rate limit 5 percobaan/menit per kombinasi username+IP.
        RateLimiter::for('login', function (Request $request) {
            $throttleKey = strtolower((string) $request->username).'|'.$request->ip();

            return Limit::perMinute(5)->by($throttleKey);
        });

        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
    }
}
