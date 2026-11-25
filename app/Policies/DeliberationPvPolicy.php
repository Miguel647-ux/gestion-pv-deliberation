<?php

namespace App\Policies;

use App\Models\DeliberationPv;
use App\Models\User;

class DeliberationPvPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isDa() || $user->isCd();
    }

    public function view(User $user, DeliberationPv $pv): bool
    {
        return $user->isDa() || $pv->program_id === $user->program_id;
    }

    public function viewFile(User $user, DeliberationPv $pv): bool
    {
        return $this->view($user, $pv);
    }

    public function create(User $user): bool
    {
        return $user->isDa() || $user->isCd();
    }

    public function update(User $user, DeliberationPv $pv): bool
    {
        if ($pv->status === DeliberationPv::STATUS_ARCHIVE) {
            return false;
        }
        return $user->isDa() || $pv->created_by === $user->id;
    }

    public function archive(User $user, DeliberationPv $pv): bool
    {
        return $user->isDa() && $pv->status === DeliberationPv::STATUS_SOUMIS;
    }

    public function delete(User $user, DeliberationPv $pv): bool
    {
        if ($user->isDa()) {
            return true;
        }
        return $pv->created_by === $user->id
            && $pv->status !== DeliberationPv::STATUS_ARCHIVE;
    }
}