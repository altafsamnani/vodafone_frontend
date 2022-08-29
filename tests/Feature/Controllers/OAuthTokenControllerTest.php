<?php

namespace Tests\Feature\Controllers;

use App\Http\Controllers\OAuthTokenController;
use App\Http\Requests\AuthenticateRequest;
use App\Http\Requests\TokenRequest;
use App\Services\Contract\OauthServiceInterface;
use Illuminate\Http\Response;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * @coversDefaultClass OAuthTokenController
 */
class OAuthTokenControllerTest extends TestCase
{
    /** @var string REFRESH_TOKEN */
    private const REFRESH_TOKEN = 'eysaazzzdysysyazz_Refresh';

    /** @var string ACCESS_TOKEN */
    private const ACCESS_TOKEN = 'eysaazzzdysysyazz_Access';

    /** @var string */
    private const ERROR_MESSAGE = 'There is an error';

    private OauthServiceInterface $keycloakService;

    private OAuthTokenController $controller;

    public function setUp(): void
    {
        $this->keycloakService = $this->createMock(OauthServiceInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        $this->controller = new OAuthTokenController($this->keycloakService, $logger);
    }

    /** @covers ::authenticate */
    public function testAuthenticate()
    {
        $requestParameters = [
            'vodafone_url' => self::REFRESH_TOKEN,
            'scope' => ['openapi'],
            'vodafone_session' => 'xyz',
            'exchange_verifier_challenge' => 'abcd',
        ];
        $this->keycloakService->expects(self::once())->method('getAuthenticateUrl')
            ->with($requestParameters)
            ->willReturn('https://oauthserver.vodafone.com');

        $response = $this->controller->authenticate(new AuthenticateRequest($requestParameters));

        $this->assertEquals(Response::HTTP_FOUND, $response->getStatusCode());
    }

    /** @covers ::authenticate */
    public function testAuthenticateFailure()
    {
        $requestParameters = [
            'vodafone_url' => self::REFRESH_TOKEN,
            'scope' => ['openapi'],
            'vodafone_session' => 'xyz',
            'exchange_verifier_challenge' => 'abcd',
        ];
        $this->keycloakService->expects(self::once())->method('getAuthenticateUrl')
            ->with($requestParameters)
            ->willThrowException(new \Exception(self::ERROR_MESSAGE));

        $request = new AuthenticateRequest($requestParameters);
        $this->expectException(\Exception::class);

        $this->controller->authenticate($request);
    }

    /** @covers ::refresh */
    public function testRefreshTokenIsReturnedSuccessfully()
    {
        $this->keycloakService->expects(self::once())->method('getRefreshToken')
            ->with(self::REFRESH_TOKEN)
            ->willReturn(['refresh_token' => self::REFRESH_TOKEN]);

        $response = $this->controller->refresh(new TokenRequest(['refresh_token' => self::REFRESH_TOKEN]));

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    /** @covers ::refresh */
    public function testRefreshTokenThrowsError()
    {
        $this->keycloakService->expects(self::once())->method('getRefreshToken')
            ->with(self::REFRESH_TOKEN)
            ->willThrowException(new \Exception(self::ERROR_MESSAGE));

        $response = $this->controller->refresh(new TokenRequest(['refresh_token' => self::REFRESH_TOKEN]));

        $this->assertJson($response->getContent());
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    /** @covers ::revoke */
    public function testRevokeTokenWithRefreshToken()
    {
        $this->keycloakService->expects(self::once())->method('revokeToken')
            ->with(self::REFRESH_TOKEN)
            ->willReturn(['@odata.context' => 'https:\/\/graph.keycloak.com\/v1.0\/$metadata#Edm.Boolean', 'value' => true]);

        $response = $this->controller->revoke(new TokenRequest(['refresh_token' => self::REFRESH_TOKEN]));

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    /** @covers ::revoke */
    public function testRevokeTokenWithAccessToken()
    {
        $this->keycloakService->expects(self::once())->method('revokeToken')
            ->with(self::REFRESH_TOKEN, self::ACCESS_TOKEN)
            ->willReturn([
                '@odata.context' => 'https:\/\/graph.keycloak.com\/v1.0\/$metadata#Edm.Boolean',
                'value' => true,
            ]);

        $response = $this->controller->revoke(new TokenRequest([
            'access_token' => self::ACCESS_TOKEN,
            'refresh_token' => self::REFRESH_TOKEN,
        ]));

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    /** @covers ::revoke */
    public function testRevokeTokenThrowsError()
    {
        $this->keycloakService->expects(self::once())->method('revokeToken')
            ->with(self::REFRESH_TOKEN, self::ACCESS_TOKEN)
            ->willThrowException(new \Exception(self::ERROR_MESSAGE));

        $response = $this->controller->revoke(new TokenRequest([
            'access_token' => self::ACCESS_TOKEN,
            'refresh_token' => self::REFRESH_TOKEN,
        ]));

        $this->assertJson($response->getContent());
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }
}
