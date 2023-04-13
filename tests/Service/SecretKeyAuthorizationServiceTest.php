<?php declare(strict_types=1);

namespace Tests\Service;

use GuzzleHttp\Psr7\HttpFactory;
use PHPUnit\Framework\TestCase;
use HBS\Auth\Authenticator\SecretKeyAuthenticator;
use HBS\Auth\Exception\AuthenticationException;
use HBS\Auth\Service\SecretKeyAuthorizationService;
use Tests\AuxiliaryClasses\Authorizer;
use Tests\AuxiliaryClasses\PayloadMapper;

final class SecretKeyAuthorizationServiceTest extends TestCase
{
    public function testAuthorize(): void
    {
        $secretKey = "\$eCrEt-KeY";
        $userId = 42;

        $bodyData = [
            'token' => $secretKey,
            'userId' => $userId,
        ];

        $httpFactory = new HttpFactory();

        $request = $httpFactory->createServerRequest('POST', 'http://localhost/')
            ->withHeader('Content-Type', 'application/json')
            ->withParsedBody($bodyData);

        $authenticator = new SecretKeyAuthenticator(
            new PayloadMapper(),
            "test.domain.tld",
            $secretKey
        );

        $authorizer = new Authorizer();

        $service = new SecretKeyAuthorizationService(
            $authenticator,
            $authorizer,
            $httpFactory,
            'token'
        );

        try {
            $service->authorize($request);
        } catch (AuthenticationException $e) {
            $this->fail("Failed to check correct secret key");
        }

        $this->assertNotNull($authorizer->getIdentity());

        $identityData = $authorizer->getIdentity()->toArray();

        $this->assertArrayHasKey('id', $identityData);
        $this->assertEquals($userId, $identityData['id']);
    }
}
