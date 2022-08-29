<?php declare(strict_types=1);

namespace Tests\Authenticator;

use HBS\Auth\Model\Identity\AbstractIdentity;

final class Identity extends AbstractIdentity
{
    protected const DOMAIN = "TEST";

    public function toArray(): array
    {
        return [];
    }
}
