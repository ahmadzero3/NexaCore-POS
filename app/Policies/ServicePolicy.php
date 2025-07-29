<?php

namespace App\Policies;

use App\Models\User;

class ServicePolicy
{
    
    public function delete(User $user) : bool
    {
        return $user->can('service.delete');
    }
}
