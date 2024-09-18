<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use Laravel\Fortify\Fortify;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //Fortify::createUsersUsing(CreateNewUser::class);
        //Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->email;

            return Limit::perMinute(5)->by($email.$request->ip());
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        Fortify::loginView(function () {
            return Inertia::render('Session/Login');
        });

        Fortify::authenticateUsing(function (Request $request) {
            $checkEmail = User::where('email', $request->email)->get();
            if($checkEmail->count() == 0){
                $user = User::where('email', $request->email)
                    ->orWhere('id', $request->email)
                    ->first();
            }else{
                $user = User::where('email', $request->email)
                    ->where('email_verified_at','!=', NULL)
                    ->orWhere('id', $request->email)
                    ->first();
            }

            if ($user &&
                Hash::check($request->password, $user->password)) {
                return $user;
            }
        });


        Fortify::resetPasswordView(function ($request) {

            return Inertia::render('Session/ResetPassword',[
                'email' => $request->email,
                'token' => $request->token,
            ]);
        });
    }
}
