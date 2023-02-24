<?php

declare(strict_types=1);
namespace StrictlyPHP\Value\Implementation\DateTime;

use Carbon\Carbon;
use StrictlyPHP\Value\Contracts\DateTime\DateInterface;
use StrictlyPHP\Value\Contracts\DateTime\Exception\InvalidDateException;
use StrictlyPHP\Value\Contracts\DateTime\TimezoneInterface;
use StrictlyPHP\Value\Contracts\ValueObjectInterface;

class Date implements DateInterface
{
    private const DATE_FORMAT = 'Y-m-d';

    private const DATE_FULL_FORMAT = 'Y-m-d H:i:s';

    private function __construct(
        private int $year,
        private int $month,
        private int $day,
    ) {
    }

    public static function fromString(string $dateString): self
    {
        $date = date_parse($dateString);

        if (
            $date['year'] === false ||
            $date['month'] === false ||
            $date['day'] === false
        ) {
            throw new \InvalidArgumentException(sprintf('%s is an invalid date', $dateString));
        }

        return new self(
            $date['year'],
            $date['month'],
            $date['day']
        );
    }

    /**
     * @throws InvalidDateException
     */
    public static function fromDateTimeUtc(DateTimeUtc $start, TimezoneInterface $timeZone): self
    {
        $date = date_parse($start->format(self::DATE_FORMAT, $timeZone));
        return new self(
            $date['year'],
            $date['month'],
            $date['day']
        );
    }

    public function getValue(): string
    {
        return sprintf(
            '%s-%s-%s',
            $this->year,
            str_pad((string) $this->month, 2, '0', STR_PAD_LEFT),
            str_pad((string) $this->day, 2, '0', STR_PAD_LEFT)
        );
    }

    public function __toString(): string
    {
        return $this->getValue();
    }

    public function toPhpDateTime(): \DateTime
    {
        return \DateTime::createFromFormat(
            self::DATE_FULL_FORMAT,
            $this->getValue() . ' 00:00:00',
            new \DateTimeZone('UTC')
        );
    }

    public static function fromPhpDateTime(\DateTime $dateTime): self
    {
        $date = date_parse($dateTime->format(self::DATE_FORMAT));
        return new self(
            $date['year'],
            $date['month'],
            $date['day']
        );
    }

    /**
     * @throws InvalidDateException
     */
    public static function fromNow(TimezoneInterface $timezone): self
    {
        $date = date_parse((new \DateTime('now', new \DateTimeZone($timezone->getValue())))->format(self::DATE_FORMAT));
        return new self(
            $date['year'],
            $date['month'],
            $date['day']
        );
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

    public function getYear(): int
    {
        return $this->year;
    }

    public function getMonth(): int
    {
        return $this->month;
    }

    public function getDay(): int
    {
        return $this->day;
    }

    public function addDays(int $days): DateInterface
    {
        $carbonDate = Carbon::createFromDate($this->year, $this->month, $this->day)->addDays($days);
        return new self(
            $carbonDate->year,
            $carbonDate->month,
            $carbonDate->day
        );
    }

    public function addMonths(int $months): DateInterface
    {
        $carbonDate = Carbon::createFromDate($this->year, $this->month, $this->day)->addMonths($months);
        return new self(
            $carbonDate->year,
            $carbonDate->month,
            $carbonDate->day
        );
    }

    public function addYears(int $years): DateInterface
    {
        $carbonDate = Carbon::createFromDate($this->year, $this->month, $this->day)->addYears($years);
        return new self(
            $carbonDate->year,
            $carbonDate->month,
            $carbonDate->day
        );
    }
}
