<?php declare(strict_types=1);

namespace Tests\Authenticator;

use HBS\Auth\Mapper\ArrayToIdentityInterface;
use HBS\Auth\Model\Identity\IdentityInterface;

final class PayloadMapper implements ArrayToIdentityInterface
{
    public function transform(array $payload, string $identityDomain): IdentityInterface
    {
        return new Identity();
    }
}
