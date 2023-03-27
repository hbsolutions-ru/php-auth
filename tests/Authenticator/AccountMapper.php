<?php declare(strict_types=1);

namespace Tests\Authenticator;

use HBS\Auth\Mapper\AccountToIdentityInterface;
use HBS\Auth\Model\Account\AccountInterface;
use HBS\Auth\Model\Identity\IdentityInterface;
use Tests\AuxiliaryClasses\Identity;

final class AccountMapper implements AccountToIdentityInterface
{
    /**
     * @inheritDoc
     */
    public function transform(AccountInterface $account, string $identityDomain): IdentityInterface
    {
        return new Identity(
            $account->getId()
        );
    }
}
