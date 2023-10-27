<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\Plane;
use App\Models\PlaneReservation;
use App\Policies\PlanePolicy;
use App\Policies\PlaneReservationPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Plane::class => PlanePolicy::class,
        PlaneReservation::class => PlaneReservationPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
