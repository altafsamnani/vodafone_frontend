<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthenticateRequest;
use App\Http\Requests\TokenRequest;
use App\Services\Contract\OauthServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

class OAuthTokenController extends Controller
{
    protected OauthServiceInterface $oAuthService;

    protected LoggerInterface $logger;

    public function __construct(OauthServiceInterface $oAuthService, LoggerInterface $logger)
    {
        $this->oAuthService = $oAuthService;
        $this->logger = $logger;
    }

    public function authenticate(AuthenticateRequest $request): RedirectResponse
    {
        $requestParameters = $request->all();
        $redirectUrl = $this->oAuthService->getAuthenticateUrl($requestParameters);

        return new RedirectResponse($redirectUrl);
    }

    public function refresh(TokenRequest $request): JsonResponse
    {
        try {
            $refreshToken = $request->get('refresh_token');
            $tokenResponse = $this->oAuthService->getRefreshToken($refreshToken);

            return new JsonResponse($tokenResponse);
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());

            return new JsonResponse(['error' => $exception->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    public function revoke(TokenRequest $request): JsonResponse
    {
        try {
            $refreshToken = $request->get('refresh_token');
            $accessToken = $request->get('access_token');
            $revokeTokenResponse = $this->oAuthService->revokeToken($refreshToken, $accessToken);

            return new JsonResponse($revokeTokenResponse);
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());

            return new JsonResponse(['error' => $exception->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    public function create(Request $request): JsonResponse
    {
        try {
            $verifier = $request->get('exchange_verifier');
            $session = $request->get('vodafone_session');
            $token = $this->oAuthService->getToken($verifier, $session);

            return new JsonResponse(json_decode($token));
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());

            return new JsonResponse(['error' => $exception->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    public function redirect(): RedirectResponse
    {
        try {
            $redirectUrl = $this->oAuthService->getRedirectUrl();
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
            $redirectUrl = $this->oAuthService->getSessionVariable('vodafone_url');
        }

        return new RedirectResponse($redirectUrl);
    }
}
