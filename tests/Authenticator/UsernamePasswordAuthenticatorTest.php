<?php declare(strict_types=1);

namespace Tests\Authenticator;

use PHPUnit\Framework\TestCase;
use HBS\Auth\Authenticator\UsernamePasswordAuthenticator;
use HBS\Auth\Exception\AuthenticationException;
use HBS\Auth\Immutable\HmacSettings;
use HBS\Auth\Model\Credentials\UsernamePassword;

final class UsernamePasswordAuthenticatorTest extends TestCase
{
    public function testAuthenticate(): void
    {
        $openPassword = "mYp@s\$w0rD";
        $userId = 42;
        $username = "john-doe";

        $userData = [
            [
                'id' => $userId,
                'username' => $username,
                'passwordHash' => "\$argon2id\$v=19\$m=65536,t=4,p=1\$cXIvcGpTVkVaWUppRjFQbQ\$EbIogIov2OMe8QC7Z3IEOGurV08dGwkcHOmwf17uCnc",
            ],
        ];

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
            $this->fail("Invalid credentials");
        }


        $identityData = $identity->toArray();
        $this->assertArrayHasKey('id', $identityData);
        $this->assertEquals($userId, $identityData['id']);
    }
}
