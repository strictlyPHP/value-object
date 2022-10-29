<?php

declare(strict_types=1);

namespace StrictlyPHP\Value\Implementation\Identity;

use Ramsey\Uuid\Uuid as RamseyUuid;
use StrictlyPHP\Value\Contracts\Identity\UuidInterface;
use StrictlyPHP\Value\Contracts\ValueObjectInterface;

class Uuid implements UuidInterface
{
    private string $value;

    private function __construct(
        string $value
    ) {
        $this->value = $value;
    }

    public static function fromRandom(): self
    {
        return new self(RamseyUuid::uuid4()->toString());
    }

    public static function fromString(string $uuid): self
    {
        if (RamseyUuid::isValid($uuid)) {
            return new Uuid($uuid);
        } else {
            throw new \InvalidArgumentException(sprintf('%s is not a uuid', $uuid));
        }
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->getValue();
    }

    public function isEqual(ValueObjectInterface $compareValueObject): bool
    {
        if (! $compareValueObject instanceof self) {
            return false;
        }
        return $this->jsonSerialize() === $compareValueObject->jsonSerialize();
    }

    public function jsonSerialize(): string
    {
        return $this->getValue();
    }
}
