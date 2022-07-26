<?php declare(strict_types=1);

namespace HBS\Auth\Model\Credentials;

interface TokenInterface extends CredentialsInterface
{
    public function getPayload(): array;

    public function getToken(): string;
}
