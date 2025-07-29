<?php

namespace App\Policies;

use App\Models\User;

class CustomerPolicy
{

    public function delete(User $user) : bool
    {
        return $user->can('customer.delete');
    }
}
