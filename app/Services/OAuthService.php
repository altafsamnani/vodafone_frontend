<?php

namespace App\Services;

use Illuminate\Cache\Repository;
use Illuminate\Contracts\Session\Session;

abstract class OAuthService
{
    protected Session $session;

    protected Repository $cache;

    public function __construct(Session $session, Repository $cache)
    {
        $this->session = $session;
        $this->cache = $cache;
    }

    public function getToken(string $verifier, string $session): mixed
    {
        $challenge = $this->cache->pull($session . '__exchange_verifier_challenge');
        $token = $this->cache->pull($session . '__token');
        if ($token === null || !$this->isVerifierValid($challenge, $verifier)) {
            throw new \UnexpectedValueException('Invalid arguments. $token:[' . $token . '], $challenge:[' . $challenge . '], $verifier:[' . $verifier .']');
        }

        return $token;
    }

    public function getSessionUrl(array $token): string
    {
        $url = $this->session->get('vodafone_url');
        $session = $this->session->get('vodafone_session');
        $this->cache->put($session . '__token', json_encode($token), 120);

        return $url;
    }

    public function getSessionVariable($key): string
    {
        return $this->session->get($key);
    }

    public function setVerifier($requestParameters): void
    {
        $this->session->put('vodafone_url', $requestParameters['vodafone_url']);
        $this->session->put('vodafone_session', $requestParameters['vodafone_session']);
        $this->cache->put($requestParameters['vodafone_session'] . '__exchange_verifier_challenge', $requestParameters['exchange_verifier_challenge'], 120);
    }

    private function isVerifierValid(string $challenge, string $verifier): bool
    {
        $hash = hash('sha256', $verifier, true);

        return $challenge == rtrim(strtr(base64_encode($hash), '+/', '-_'), '=');
    }

    abstract protected function getDriver();

}
