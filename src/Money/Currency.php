<?php

declare(strict_types=1);

namespace StrictlyPHP\Value\Implementation\Money;

use Money\Currencies\ISOCurrencies;
use StrictlyPHP\Value\Contracts\Money\CurrencyInterface;
use StrictlyPHP\Value\Contracts\Money\Exception\InvalidCurrencyException;
use StrictlyPHP\Value\Contracts\ValueObjectInterface;

class Currency implements CurrencyInterface
{
    private string $value;

    private function __construct(
        string $value
    ) {
        $this->value = $value;
    }

    public static function fromString(string $value): self
    {
        $value = strtoupper($value);
        if (empty($value)) {
            throw new InvalidCurrencyException('currency not set');
        }

        $currencies = new ISOCurrencies();
        if (! $currencies->contains(new \Money\Currency($value))) {
            throw new InvalidCurrencyException(sprintf('currency %s is not supported', $value));
        }
        return new self(
            $value
        );
    }

    public function jsonSerialize(): string
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

    public function getValue(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->getValue();
    }
}
