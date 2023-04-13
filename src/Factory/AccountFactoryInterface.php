<?php declare(strict_types=1);

namespace HBS\Auth\Factory;

use HBS\Auth\Model\Account\AccountInterface;
use HBS\Auth\Model\Credentials\UsernamePassword;

interface AccountFactoryInterface
{
    public function create(UsernamePassword $credentials): AccountInterface;
}
