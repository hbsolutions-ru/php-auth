<?php declare(strict_types=1);

namespace Tests\Authenticator;

use HBS\Helpers\ArrayHelper;
use HBS\Auth\Model\Account\AccountInterface;
use HBS\Auth\Repository\AccountRepositoryInterface;

final class AccountRepository implements AccountRepositoryInterface
{
    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function getByUsername(string $username): AccountInterface
    {
        $data = ArrayHelper::keysFromColumn($this->data, 'username');

        if (!isset($data[$username])) {
            throw new \RuntimeException("User not found");
        }

        return new Account(
            intval($data[$username]['id'] ?? 0),
            (string)($data[$username]['username'] ?? ''),
            (string)($data[$username]['passwordHash'] ?? '')
        );
    }
}
