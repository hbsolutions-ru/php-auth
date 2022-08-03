<?php declare(strict_types=1);

namespace HBS\Auth\Authorizer;

use HBS\Auth\Model\Identity\IdentityInterface;

interface AuthorizerInterface
{
    /**
     * Authorize specified identity
     *
     * @param IdentityInterface $identity
     */
    public function authorize(IdentityInterface $identity): void;
}
