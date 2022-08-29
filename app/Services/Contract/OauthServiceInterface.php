<?php

namespace App\Services\Contract;

interface OauthServiceInterface
{
    public function getRedirectUrl(): string;

    public function getRefreshToken(string $refreshToken): mixed;

    public function revokeToken(string $refreshToken, ?string $accessToken = null): mixed;

    public function getAuthenticateUrl($requestParameters): string;
}
