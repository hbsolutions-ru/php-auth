<?php declare(strict_types=1);

namespace HBS\Auth\Repository;

use HBS\Auth\Model\Account\AccountEntityInterface;

interface AccountRepositoryInterface
{
    public function getByUsername(string $username): AccountEntityInterface;
}
