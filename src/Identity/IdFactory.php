<?php

declare(strict_types=1);

namespace StrictlyPHP\Value\Implementation\Identity;

use StrictlyPHP\Value\Contracts\Identity\IdFactoryInterface;
use StrictlyPHP\Value\Contracts\Identity\UuidInterface;

class IdFactory implements IdFactoryInterface
{
    public function generateNewUuId(): UuidInterface
    {
        return Uuid::fromRandom();
    }
}
