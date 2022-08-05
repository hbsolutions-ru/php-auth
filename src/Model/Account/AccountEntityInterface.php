<?php declare(strict_types=1);

namespace HBS\Auth\Model\Account;

interface AccountEntityInterface
{
    public function getId(): int;

    public function getUsername(): string;

    public function getPasswordHash(): string;
}
