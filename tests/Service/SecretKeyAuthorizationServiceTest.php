<?php declare(strict_types=1);

namespace Tests\Service;

use Slim\Psr7\Factory\{
    RequestFactory,
    ResponseFactory,
};
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

        $requestFactory = new RequestFactory();
        $responseFactory = new ResponseFactory();

        $request = $requestFactory->createRequest('POST', 'http://localhost/')
            ->withHeader('Content-Type', 'application/json')
            ->withParsedBody($bodyData);

        $authenticator = new SecretKeyAuthenticator(
            new PayloadMapper(),
            "TEST",
            $secretKey
        );

        $authorizer = new Authorizer();

        $service = new SecretKeyAuthorizationService(
            $authenticator,
            $authorizer,
            $responseFactory,
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
