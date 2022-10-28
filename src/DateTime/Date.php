<?php

declare(strict_types=1);
namespace StrictlyPHP\Value\Implementation\DateTime;

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
        private int $day
    ) {
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
}
