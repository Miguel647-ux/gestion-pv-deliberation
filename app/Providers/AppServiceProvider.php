<?php

namespace App\Providers;

use App\Models\DeliberationPv;
use App\Policies\DeliberationPvPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(DeliberationPv::class, DeliberationPvPolicy::class);
    }
}