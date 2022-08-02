<?php declare(strict_types=1);

namespace HBS\Auth\Model\Identity;

interface IdentityInterface
{
    /**
     * Domain is the targeted subject area here, not a web URL
     *
     * @return string
     */
    public function getDomain(): string;
}
