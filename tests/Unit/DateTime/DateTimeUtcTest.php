<?php

declare(strict_types=1);

/*
 * Copyright © 2017-present The Stack World. All rights reserved.
 */

namespace App\Tests\Unit\DateTime;

use StrictlyPHP\Value\Implementation\DateTime\DateTimeUtc;
use StrictlyPHP\Value\Implementation\DateTime\Timezone;
use PHPUnit\Framework\TestCase;

class DateTimeUtcTest extends TestCase
{
    /**
     * @dataProvider timeStampProvider
     */
    public function testFromTimeStampSucceeds(int $timestamp, string $formattedDate)
    {
        $dateTime = DateTimeUtc::fromTimestamp($timestamp);
        $this->assertEquals($formattedDate, $dateTime->getValue());
    }

    /**
     * @dataProvider timeStampMsProvider
     */
    public function testFromTimestampMsSucceeds(int $timestampMs, string $formattedDate)
    {
        $dateTime = DateTimeUtc::fromTimestampMs($timestampMs);
        $this->assertEquals($formattedDate, $dateTime->getValue());
    }

    /**
     * @dataProvider isBeforeProvider
     */
    public function testIsBeforeSucceeds(DateTimeUtc $dateA, DateTimeUtc $dateB, bool $inclusive, bool $isBefore)
    {
        $this->assertEquals($isBefore, $dateA->isBefore($dateB, $inclusive));
    }

    /**
     * @dataProvider isAfterProvider
     */
    public function testIsAfterSucceeds(DateTimeUtc $dateA, DateTimeUtc $dateB, bool $inclusive, bool $isAfter)
    {
        $this->assertEquals($isAfter, $dateA->isAfter($dateB, $inclusive));
    }

    public function testFirstDayOfLastMonth(): void
    {
        $date = DateTimeUtc::fromString('2022-07-01 00:00:00', Timezone::fromString('Europe/London'))->firstDayOfLastMonth(Timezone::fromString('Europe/London'));
        self::assertEquals('2022-06-01 00:00:00', $date->format('Y-m-d H:i:s', Timezone::fromString('Europe/London')));
    }

    public function testFirstDayOfNextMonth(): void
    {
        $date = DateTimeUtc::fromString('2022-07-01 00:00:00', Timezone::fromString('Europe/London'))->firstDayOfNextMonth(Timezone::fromString('Europe/London'));
        self::assertEquals('2022-08-01 00:00:00', $date->format('Y-m-d H:i:s', Timezone::fromString('Europe/London')));
    }

    public function testLastDayOfThisMonth(): void
    {
        $date = DateTimeUtc::fromString('2022-07-11 00:00:00', Timezone::fromString('Europe/London'))->lastDayOfThisMonth(Timezone::fromString('Europe/London'));
        self::assertEquals('2022-07-31 00:00:00', $date->format('Y-m-d H:i:s', Timezone::fromString('Europe/London')));
    }

    public function testFirstDayOfThisMonth(): void
    {
        $date = DateTimeUtc::fromString('2022-07-11 00:00:00', Timezone::fromString('Europe/London'))->firstDayOfThisMonth(Timezone::fromString('Europe/London'));
        self::assertEquals('2022-07-01 00:00:00', $date->format('Y-m-d H:i:s', Timezone::fromString('Europe/London')));
    }

    public function testFromInterval(): void
    {
        $date = DateTimeUtc::fromInterval(100, DateTimeUtc::fromString('2022-07-11 00:00:00'));
        self::assertEquals('2022-07-11T00:01:40+00:00', $date->getValue());
    }

    public function testRemoveInterval(): void
    {
        $date = DateTimeUtc::fromString('2022-07-11 00:00:00')->removeInterval(100);
        self::assertEquals('2022-07-10T23:58:20+00:00', $date->getValue());
    }

    public function timeSlotProvider(): array
    {
        return [
            ['2017-06-29T09:00:00+00:00', '2017-06-29T09:00:00+00:00', null],
            ['2017-06-29T09:00:00+01:00', '2017-06-29T08:00:00+00:00', null],
            ['2017-06-29T00:00:00+02:00', '2017-06-28T22:00:00+00:00', null],
            ['2017-06-29T00:00:00', '2017-06-28T23:00:00+00:00', 'Europe/London'],
            ['2017-06-29T00:00:00', '2017-06-29T04:00:00+00:00', 'America/Toronto'],
        ];
    }

    public function timeStampProvider(): array
    {
        return [
            [0, '1970-01-01T00:00:00+00:00'],
            [1525268528, '2018-05-02T13:42:08+00:00'],
        ];
    }

    public function timeStampMsProvider(): array
    {
        return [
            [0, '1970-01-01T00:00:00+00:00'],
            [1525268528000, '2018-05-02T13:42:08+00:00'],
            [1627555002512, '2021-07-29T10:36:42+00:00'],
        ];
    }

    public function isBeforeProvider(): array
    {
        // time A, Time B, inclusive, result
        return [
            [DateTimeUtc::fromString('1970-01-01T00:00:00+00:00'), DateTimeUtc::fromString('2018-05-02T13:42:08+00:00'), false, true],
            [DateTimeUtc::fromString('2018-05-02T13:42:08+00:00'), DateTimeUtc::fromString('1970-01-01T00:00:00+00:00'), false, false],
            [DateTimeUtc::fromString('2018-05-02T13:42:08+00:00'), DateTimeUtc::fromString('2018-05-02T13:42:08+00:00'), false, false],
            [DateTimeUtc::fromString('1970-01-01T00:00:00+00:00'), DateTimeUtc::fromString('2018-05-02T13:42:08+00:00'), true, true],
            [DateTimeUtc::fromString('2018-05-02T13:42:08+00:00'), DateTimeUtc::fromString('1970-01-01T00:00:00+00:00'), true, false],
            [DateTimeUtc::fromString('2018-05-02T13:42:08+00:00'), DateTimeUtc::fromString('2018-05-02T13:42:08+00:00'), true, true],
        ];
    }

    public function isAfterProvider()
    {
        // time A, Time B, inclusive, result
        return [
            [DateTimeUtc::fromString('1970-01-01T00:00:00+00:00'), DateTimeUtc::fromString('2018-05-02T13:42:08+00:00'), false, false],
            [DateTimeUtc::fromString('2018-05-02T13:42:08+00:00'), DateTimeUtc::fromString('1970-01-01T00:00:00+00:00'), false, true],
            [DateTimeUtc::fromString('2018-05-02T13:42:08+00:00'), DateTimeUtc::fromString('2018-05-02T13:42:08+00:00'), false, false],
            [DateTimeUtc::fromString('1970-01-01T00:00:00+00:00'), DateTimeUtc::fromString('2018-05-02T13:42:08+00:00'), true, false],
            [DateTimeUtc::fromString('2018-05-02T13:42:08+00:00'), DateTimeUtc::fromString('1970-01-01T00:00:00+00:00'), true, true],
            [DateTimeUtc::fromString('2018-05-02T13:42:08+00:00'), DateTimeUtc::fromString('2018-05-02T13:42:08+00:00'), true, true],
        ];
    }
}
