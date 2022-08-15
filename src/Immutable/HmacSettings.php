<?php declare(strict_types=1);

namespace HBS\Auth\Immutable;

use HBS\Immutable\AbstractImmutable;

/**
 * @property string $algorithm The signing algorithm
 * @property string $secret The secret key
 */
final class HmacSettings extends AbstractImmutable
{
    private const PROPERTY_ALGORITHM = 'algorithm';
    private const PROPERTY_SECRET = 'secret';

    public function __construct(string $algorithm, string $secret)
    {
        $this->data[self::PROPERTY_ALGORITHM] = $algorithm;
        $this->data[self::PROPERTY_SECRET] = $secret;

        parent::__construct();
    }
}
