<?php declare(strict_types=1);

namespace Tests\Authenticator;

use HBS\Auth\Mapper\AccountEntityToIdentityInterface;
use HBS\Auth\Model\Account\AccountEntityInterface;
use HBS\Auth\Model\Identity\IdentityInterface;
use Tests\AuxiliaryClasses\Identity;

final class AccountEntityMapper implements AccountEntityToIdentityInterface
{
    /**
     * @inheritDoc
     */
    public function transform(AccountEntityInterface $accountEntity, string $identityDomain): IdentityInterface
    {
        return new Identity(
            $accountEntity->getId()
        );
    }
}
