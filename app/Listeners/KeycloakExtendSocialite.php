<?php

namespace App\Listeners;

use App\Services\OAuth\Keycloak\KeycloakSocialiteProvider;
use SocialiteProviders\Manager\SocialiteWasCalled;

class KeycloakExtendSocialite
{
    /** Register the provider. */
    public function handle(SocialiteWasCalled $socialiteWasCalled)
    {
        $socialiteWasCalled->extendSocialite('keycloak', KeycloakSocialiteProvider::class);
    }
}
