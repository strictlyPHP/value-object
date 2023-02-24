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

    /**
     * @dataProvider daysProvider
     */
    public function testItAddsDays(string $startDateString, string $endDateString, int $daysToAdd): void
    {
        $date = Date::fromString($startDateString);
        $newDate = $date->addDays($daysToAdd);
        self::assertEquals($endDateString, $newDate->getValue());
    }

    /**
     * @dataProvider monthsProvider
     */
    public function testItAddsMonths(string $startDateString, string $endDateString, int $monthsToAdd): void
    {
        $date = Date::fromString($startDateString);
        $newDate = $date->addMonths($monthsToAdd);
        self::assertEquals($endDateString, $newDate->getValue());
    }

    public function testItAddsYears(): void
    {
        $date = Date::fromString('2023-01-01');
        $newDate = $date->addYears(3);
        self::assertEquals('2026-01-01', $newDate->getValue());
    }

    public function daysProvider(): array
    {
        return [
            ['2023-01-01', '2023-01-11', 10],
            ['2023-01-20', '2023-02-04', 15],
            ['2023-02-20', '2023-03-07', 15],
            ['2024-02-20', '2024-03-06', 15],
            ['2023-03-20', '2023-04-04', 15],
            ['2023-04-20', '2023-05-05', 15],
            ['2023-05-20', '2023-06-04', 15],
            ['2023-06-20', '2023-07-05', 15],
            ['2023-07-20', '2023-08-04', 15],
            ['2023-08-20', '2023-09-04', 15],
            ['2023-09-20', '2023-10-05', 15],
            ['2023-10-20', '2023-11-04', 15],
            ['2023-11-20', '2023-12-05', 15],
            ['2023-12-20', '2024-01-04', 15],
        ];
    }

    public function monthsProvider(): array
    {
        return [
            ['2023-01-01', '2023-11-01', 10],
            ['2023-01-01', '2024-04-01', 15],
        ];
    }
}
