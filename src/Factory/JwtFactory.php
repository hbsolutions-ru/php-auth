<?php declare(strict_types=1);

namespace HBS\Auth\Factory;

use HBS\Auth\{
    Immutable\JwtSettings,
    Model\Credentials\Jwt,
    Model\Credentials\JwtInterface,
};

class JwtFactory implements JwtFactoryInterface
{
    protected JwtSettings $settings;

    public function __construct(JwtSettings $settings)
    {
        $this->settings = $settings;
    }

    public function create(array $payload = []): JwtInterface
    {
        return new Jwt($this->settings, $payload);
    }
}
