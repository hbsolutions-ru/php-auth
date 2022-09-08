<?php declare(strict_types=1);

namespace Tests\Authenticator;

use PHPUnit\Framework\TestCase;
use HBS\Auth\Authenticator\UsernamePasswordAuthenticator;
use HBS\Auth\Exception\AuthenticationException;
use HBS\Auth\Immutable\HmacSettings;
use HBS\Auth\Model\Credentials\UsernamePassword;

final class UsernamePasswordAuthenticatorTest extends TestCase
{
    private function getTestData(): array
    {
        return [
            [
                'id' => 42,
                'username' => "john-doe",
                'passwordHash' => "\$argon2id\$v=19\$m=65536,t=4,p=1\$cXIvcGpTVkVaWUppRjFQbQ\$EbIogIov2OMe8QC7Z3IEOGurV08dGwkcHOmwf17uCnc",
            ],
        ];
    }

    public function testAuthenticateSuccessful(): void
    {
        $userData = $this->getTestData();

        $openPassword = "mYp@s\$w0rD";
        $userId = $userData[0]['id'];
        $username = $userData[0]['username'];

        $mapper = new AccountEntityMapper();
        $repository = new AccountRepository($userData);
        $settings = new HmacSettings("sha3-512", "\$eCrEt-KeY");

        $authenticator = new UsernamePasswordAuthenticator(
            $mapper,
            $repository,
            "TEST",
            $settings
        );

        $credentials = new UsernamePassword($username, $openPassword);

        try {
            $identity = $authenticator->authenticate($credentials);
        } catch (AuthenticationException $e) {
            $this->fail("Wrong credentials");
        }


        $identityData = $identity->toArray();
        $this->assertArrayHasKey('id', $identityData);
        $this->assertEquals($userId, $identityData['id']);
    }

    public function testAuthenticateWrongCredentials(): void
    {
        $userData = $this->getTestData();

        $openPassword = "wrong-password";
        $username = $userData[0]['username'];

        $mapper = new AccountEntityMapper();
        $repository = new AccountRepository($userData);
        $settings = new HmacSettings("sha3-512", "\$eCrEt-KeY");

        $authenticator = new UsernamePasswordAuthenticator(
            $mapper,
            $repository,
            "TEST",
            $settings
        );

        $credentials = new UsernamePassword($username, $openPassword);

        try {
            $authenticator->authenticate($credentials);
            $this->fail("Exception not thrown but expected");
        } catch (AuthenticationException $e) {
            $this->assertStringContainsString("Wrong credentials", $e->getMessage());
        }
    }

    public function testAuthenticateUserNotFound()
    {
        $userData = $this->getTestData();

        $openPassword = "mYp@s\$w0rD";
        $username = "robert-roe";

        $mapper = new AccountEntityMapper();
        $repository = new AccountRepository($userData);
        $settings = new HmacSettings("sha3-512", "\$eCrEt-KeY");

        $authenticator = new UsernamePasswordAuthenticator(
            $mapper,
            $repository,
            "TEST",
            $settings
        );

        $credentials = new UsernamePassword($username, $openPassword);

        try {
            $authenticator->authenticate($credentials);
            $this->fail("Exception not thrown but expected");
        } catch (AuthenticationException $e) {
            $this->assertStringContainsString("Wrong credentials", $e->getMessage());
        }
    }
}
