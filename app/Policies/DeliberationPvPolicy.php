<?php

namespace App\Policies;

use App\Models\DeliberationPv;
use App\Models\User;

class DeliberationPvPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['da', 'cd'], true);
    }

    public function view(User $user, DeliberationPv $pv): bool
    {
        if ($user->role === 'da') {
            return true;
        }

        if ($user->role === 'cd') {
            return (int) $pv->specialty_id === (int) $user->specialty_id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['da', 'cd'], true);
    }

    public function update(User $user, DeliberationPv $pv): bool
    {
        return $this->view($user, $pv);
    }

    public function delete(User $user, DeliberationPv $pv): bool
    {
        return $user->role === 'da';
    }
}