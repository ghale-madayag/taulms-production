<?php

namespace App\Http\Middleware;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request): array
    {
        if(Auth::check()){
            $user = User::find(auth()->user()->id);
            $role = $user->getRoleNames();
        }

        return array_merge(parent::share($request), [
            
            'auth' => auth()->user() ?
                [
                    'email' => auth()->user()->email,
                    'fullname' => auth()->user()->fname.' '.auth()->user()->initial.' '.auth()->user()->lname,
                    'fname' => auth()->user()->fname,
                    'initial' => auth()->user()->initial,
                    'lname' => auth()->user()->lname,
                    'extname' => auth()->user()->extname,
                    'id' => auth()->user()->id,
                    'role' => $role[0],
                    'url' => $role[0],
                    'permission' => $user->getPermissionsViaRoles()->pluck('name'),
                    'course' => fn () => $request->session()->get('course')
                ]
                : null,
            'flash' => [
                'message' => fn () => $request->session()->get('message'),
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
                'status' => fn () => $request->session()->get('status'),
                'scoreAndTotal' => fn () => $request->session()->get('scoreAndTotal'),
                ],
        ]);
    }
}
