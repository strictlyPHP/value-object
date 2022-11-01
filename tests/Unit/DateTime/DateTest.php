<?php

declare(strict_types=1);

namespace App\Tests\Unit\DateTime;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use StrictlyPHP\Value\Implementation\DateTime\Date;
use StrictlyPHP\Value\Implementation\DateTime\DateTimeUtc;
use StrictlyPHP\Value\Implementation\DateTime\Timezone;

class DateTest extends TestCase
{
    public function testFromString(): void
    {
        $date = Date::fromString('01-03-2022');
        self::assertEquals(2022, $date->getYear());
        self::assertEquals(3, $date->getMonth());
        self::assertEquals(1, $date->getDay());
    }

    public function testFromStringFails(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Date::fromString('hello!');
    }

    public function testFromDateTimeUtcSeptember(): void
    {
        $dateimeUtc = DateTimeUtc::fromString('2022-10-03 15:49:18', Timezone::fromString('Europe/London'));
        $dateUK = Date::fromDateTimeUtc($dateimeUtc, Timezone::fromString('Europe/London'));
        $dateAus = Date::fromDateTimeUtc($dateimeUtc, Timezone::fromString('Australia/Melbourne'));
        self::assertEquals('2022-10-03', $dateUK->getValue());
        self::assertEquals('2022-10-04', $dateAus->getValue());
    }

    public function testFromPhpDateTimeSeptember(): void
    {
        $dateimeUk = \DateTime::createFromFormat('Y-m-d H:i:s', '2022-10-03 15:49:18', new \DateTimezone('Europe/London'));
        $dateimeAus = (\DateTime::createFromFormat('Y-m-d H:i:s', '2022-10-03 15:49:18', new \DateTimezone('Europe/London')))->setTimezone(new \DateTimezone('Australia/Melbourne'));
        $dateUK = Date::fromPhpDateTime($dateimeUk);
        $dateAus = Date::fromPhpDateTime($dateimeAus);
        self::assertEquals('2022-10-03', $dateUK->getValue());
        self::assertEquals('2022-10-04', $dateAus->getValue());
    }

    public function testFromNow(): void
    {
        $dateUK = Date::fromNow(Timezone::fromString('Europe/London'));
        $phpDateTime = new \DateTime('now', new \DateTimeZone('Europe/London'));
        self::assertEquals($phpDateTime->format('Y-m-d'), $dateUK->getValue());
    }

    public function testItSerializesCorrectly(): void
    {
        $dateimeUtc = Date::fromDateTimeUtc(DateTimeUtc::fromString('2022-10-03 15:49:18', Timezone::fromString('Europe/London')), Timezone::fromString('Europe/London'));
        self::assertEquals('"2022-10-03"', json_encode($dateimeUtc));
    }

    public function testItConvertsToAString(): void
    {
        $dateimeUtc = Date::fromDateTimeUtc(DateTimeUtc::fromString('2022-10-03 15:49:18', Timezone::fromString('Europe/London')), Timezone::fromString('Europe/London'));
        self::assertEquals('2022-10-03', (string) $dateimeUtc);
    }
}
