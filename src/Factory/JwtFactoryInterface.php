<?php declare(strict_types=1);

namespace HBS\Auth\Factory;

use HBS\Auth\Model\Credentials\JwtInterface;

interface JwtFactoryInterface
{
    public function create(array $payload = []): JwtInterface;
}
