<?php declare(strict_types=1);

namespace HBS\Auth\Model\Account;

interface AccountInterface
{
    public function getId(): int;

    public function getUsername(): string;

    public function getPasswordHash(): string;
}
