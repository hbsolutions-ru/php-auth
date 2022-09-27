<?php declare(strict_types=1);

namespace Tests\AuxiliaryClasses;

use HBS\Auth\Authorizer\AuthorizerInterface;
use HBS\Auth\Model\Identity\IdentityInterface;

final class Authorizer implements AuthorizerInterface
{
    private IdentityInterface $identity;

    public function authorize(IdentityInterface $identity): void
    {
        $this->identity = $identity;
    }

    public function getIdentity(): ?IdentityInterface
    {
        return $this->identity;
    }
}
