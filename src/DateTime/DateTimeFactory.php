<?php

declare(strict_types=1);

namespace StrictlyPHP\Value\Implementation\DateTime;

use StrictlyPHP\Value\Contracts\DateTime\DateTimeFactoryInterface;

class DateTimeFactory implements DateTimeFactoryInterface
{
    public function dateTimeNow(): DateTimeUtc
    {
        return DateTimeUtc::fromNow();
    }
}
