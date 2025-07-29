<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function delete(User $user) : bool
    {
        return $user->can('user.delete');
    }
}
