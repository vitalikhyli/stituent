<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        \App\Person::class        => \App\Policies\PersonPolicy::class,
        \App\Entity::class        => \App\Policies\EntityPolicy::class,
        \App\Group::class         => \App\Policies\GroupPolicy::class,
        \App\WorkCase::class      => \App\Policies\WorkCasePolicy::class,
        \App\Contact::class       => \App\Policies\ContactPolicy::class,
        \App\Relationship::class  => \App\Policies\RelationshipPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
