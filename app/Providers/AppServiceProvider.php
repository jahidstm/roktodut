<?php

namespace App\Providers;

use App\Models\BloodRequest;
use App\Policies\BloodRequestPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        BloodRequest::class => BloodRequestPolicy::class,
    ];

    public function boot(): void
    {
        //
    }
}