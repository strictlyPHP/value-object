<?php

declare(strict_types=1);

namespace App\Tests\Unit\Money;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use StrictlyPHP\Value\Contracts\Money\Exception\InvalidCurrencyException;
use StrictlyPHP\Value\Implementation\Money\Currency;
use StrictlyPHP\Value\Implementation\Money\Money;

class MoneyTest extends TestCase
{
    public function testFromValuesThrowsInvalidArgumentException()
    {
        $this->expectException(InvalidArgumentException::class);
        Money::fromStrings('1', '');
    }

    public function testFromValuesThrowsInvalidCurrencyException()
    {
        $this->expectException(InvalidCurrencyException::class);
        Money::fromStrings('1', 'HELLO');
    }

    public function testNewLowerCaseSucceeds()
    {
        $money = Money::fromStrings('1', 'gbp');
        static::assertEquals('GBP', $money->getCurrency()->getValue());
    }

    public function testFromStringsSucceeds()
    {
        $money = Money::fromStrings('1', 'GBP');
        static::assertEquals('GBP', $money->getCurrency()->getValue());
    }

    public function testFormatFromStringsSucceeds()
    {
        $money = Money::fromStrings('10', 'GBP');
        static::assertEquals('£10.00', $money->format());
    }

    public function testFormatFromIntSucceeds()
    {
        $money = Money::fromInt(1000, Currency::fromString('GBP'));
        static::assertEquals('£10.00', $money->format());
    }

    public function testSubtract(): void
    {
        $money = Money::fromInt(1000, Currency::fromString('GBP'))->subtract(Money::fromInt(299, Currency::fromString('GBP')));
        static::assertEquals('£7.01', $money->format());
    }

    public function testAdd(): void
    {
        $money = Money::fromInt(1000, Currency::fromString('GBP'))->add(Money::fromInt(299, Currency::fromString('GBP')));
        static::assertEquals('£12.99', $money->format());
    }

    public function testPercent(): void
    {
        [$money1, $money2] = Money::fromInt(1000, Currency::fromString('GBP'))->percent(33, 66);
        static::assertEquals('£3.33', $money1->format());
        static::assertEquals('£6.67', $money2->format());
    }
}
