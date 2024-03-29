<?php declare(strict_types=1);

namespace Tests\AuxiliaryClasses;

use HBS\Auth\Model\Identity\AbstractIdentity;

final class Identity extends AbstractIdentity
{
    protected const DOMAIN = "test.domain.tld";

    private int $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
        ];
    }
}
