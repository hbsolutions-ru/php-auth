<?php declare(strict_types=1);

namespace HBS\Auth\Mapper;

use HBS\Auth\{
    Model\Account\AccountEntityInterface,
    Model\Identity\IdentityInterface,
};

interface AccountEntityToIdentityInterface
{
    /**
     * @param AccountEntityInterface $accountEntity
     * @param string $identityDomain
     * @return IdentityInterface
     */
    public function transform(AccountEntityInterface $accountEntity, string $identityDomain): IdentityInterface;
}
