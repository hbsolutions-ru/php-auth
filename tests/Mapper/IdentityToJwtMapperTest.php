<?php declare(strict_types=1);

namespace Tests\Mapper;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use HBS\Helpers\ObjectHelper;
use PHPUnit\Framework\TestCase;

use HBS\Auth\Immutable\JwtSettings;
use HBS\Auth\Mapper\IdentityToJwtMapper;
use HBS\Auth\Model\Credentials\Token;
use Tests\AuxiliaryClasses\Identity;

final class IdentityToJwtMapperTest extends TestCase
{
    public function testTransform(): void
    {
        $algorithm = "HS256";
        $expiration = 3600;
        $secret = "JwT-\$eCrEt-KeY";
        $userId = 42;

        $identity = new Identity($userId);
        $settings = new JwtSettings($algorithm, $expiration, $secret);

        $mapper = new IdentityToJwtMapper($settings);

        $now = time();

        $credentials = $mapper->transform($identity);

        $this->assertInstanceOf(Token::class, $credentials);

        $key = new Key($secret, $algorithm);
        $data = ObjectHelper::toArray(
            JWT::decode($credentials->getToken(), $key)
        );

        $this->assertIsArray($data);

        $this->assertArrayHasKey('iat', $data);
        $this->assertEquals($now, $data['iat']);

        $this->assertArrayHasKey('exp', $data);
        $this->assertEquals($now + $expiration, $data['exp']);

        $this->assertArrayHasKey('data', $data);
        $this->assertIsArray($data['data']);
        $this->assertArrayHasKey('id', $data['data']);
        $this->assertEquals($userId, $data['data']['id']);
    }
}
