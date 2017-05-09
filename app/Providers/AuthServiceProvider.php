<?php

namespace App\Providers;

use Barryvdh\Debugbar\Facade as Debugbar;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Validator;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Custom validator for locking user registrations to a domain
        Validator::extend('maildomain', function ($attribute, $value, $parameters, $validator) {
            // Domain restriction can be specified by the validator rule, ie
            // maildomain:example.com or it can be set in the config
            $userRestrictionDomain = ($parameters) ? array_shift($parameters) : config('auth.restrictions.maildomain', false);

            // If there is no domain to check against, we can't do anything
            // Throw Exception?
            if (!$userRestrictionDomain) {
                Debugbar::debug('no domain restriction on email');
                Debugbar::debug(config('auth'));
                return true;
            }

            // Compare the provided email against the provided domain restriction
            $explodedEmail = explode('@', $value);
            $domain = array_pop($explodedEmail);

            // The ruled is passed if the domain matches
            return $domain == $userRestrictionDomain;
        });
    }
}
