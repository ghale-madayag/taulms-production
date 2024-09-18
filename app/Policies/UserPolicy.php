<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function view(User $user)
    {
        $can = auth()->user()->roles->first()->id;
        return $can != '1';
    }

    public function viewAny(User $user)
    {
        return true;
    }
}
