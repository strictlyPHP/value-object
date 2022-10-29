<?php

declare(strict_types=1);

namespace App\Tests\Unit\DateTime;

use PHPUnit\Framework\TestCase;
use StrictlyPHP\Value\Contracts\DateTime\DateTimeInterface;
use StrictlyPHP\Value\Implementation\DateTime\DateTimeFactory;

class DateTimeFactoryTest extends TestCase
{
    private DateTimeFactory $dateTimeFactory;

    public function setUp(): void
    {
        parent::setUp();
        $this->dateTimeFactory = new DateTimeFactory();
    }

    public function testDateTimeNow(): void
    {
        $timestamp = time();
        $now = $this->dateTimeFactory->dateTimeNow();
        //check that the times are within two seconds with each other
        self::assertTrue(abs($timestamp - $now->getTimestamp()) < 2);
    }
}
