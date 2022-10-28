<?php

declare(strict_types=1);

namespace StrictlyPHP\Value\Implementation\Identity;

use StrictlyPHP\Value\Contracts\Identity\IdInterface;
use StrictlyPHP\Value\Contracts\ValueObjectInterface;
use Ramsey\Uuid\Uuid;

class Id implements IdInterface
{
    private string $value;

    private function __construct(
        string $value
    ) {
        $this->value = $value;
    }

    public static function fromRandom(): self
    {
        return new self(Uuid::uuid4()->toString());
    }

    public static function fromUuid(string $uuid): self
    {
        if (1 === preg_match('/^(\w{8}(-\w{4}){3}-\w{12}?)/i', $uuid)) {
            return new Id($uuid);
        } else {
            throw new \InvalidArgumentException(sprintf('%s is not a uuid', $uuid));
        }
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function isUuid(): bool
    {
        return Uuid::isValid($this->value);
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
