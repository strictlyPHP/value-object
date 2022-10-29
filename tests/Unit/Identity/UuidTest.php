<?php

declare(strict_types=1);

namespace App\Tests\Unit\Identity;

use PHPUnit\Framework\TestCase;
use StrictlyPHP\Value\Implementation\Identity\Uuid;

class UuidTest extends TestCase
{
    public function testFromRandomSucceeds(): void
    {
        self::assertTrue((bool) preg_match('/^(\w{8}(-\w{4}){3}-\w{12}?)/i', Uuid::fromRandom()->getValue()));
    }

    public function testFromStringsSucceeds(): void
    {
        $string = '685e227f-8984-4706-82ea-8df66044c459';
        $uuid = Uuid::fromString($string);
        self::assertEquals($string, $uuid->getValue());
    }
}
