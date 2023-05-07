<?php declare(strict_types=1);

namespace Tests\Service\Utility;

use PHPUnit\Framework\TestCase;
use HBS\Auth\Immutable\HmacSettings;
use HBS\Auth\Service\Utility\PasswordHasher;

use function hash_hmac;
use function password_verify;

final class PasswordHasherTest extends TestCase
{
    public function testGet(): void
    {
        $password = "mYp@s\$w0rD";
        $settings = new HmacSettings("sha3-512", "\$eCrEt-KeY");
        $peppered = hash_hmac($settings->algorithm, $password, $settings->secret);

        $hasher = new PasswordHasher($settings);

        $hash = $hasher->get($password);

        $this->assertTrue(password_verify($peppered, $hash));
    }
}
