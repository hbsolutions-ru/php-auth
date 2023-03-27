<?php declare(strict_types=1);

namespace HBS\Auth\Model\Identity;

use HBS\Auth\Exception\MisconfigurationException;

abstract class AbstractIdentity implements IdentityInterface
{
    /**
     * @var string
     */
    protected const DOMAIN = null;

    public function getDomain(): string
    {
        $domain = static::DOMAIN;

        if (empty($domain) || !\is_string($domain)) {
            throw new MisconfigurationException("Identity Domain not defined");
        }

        return $domain;
    }
}
