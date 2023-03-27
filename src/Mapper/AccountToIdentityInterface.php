<?php declare(strict_types=1);

namespace HBS\Auth\Mapper;

use HBS\Auth\{
    Model\Account\AccountInterface,
    Model\Identity\IdentityInterface,
};

interface AccountToIdentityInterface
{
    /**
     * @param AccountInterface $account
     * @param string $identityDomain
     * @return IdentityInterface
     */
    public function transform(AccountInterface $account, string $identityDomain): IdentityInterface;
}
