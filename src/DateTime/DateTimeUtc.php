<?php

declare(strict_types=1);
namespace StrictlyPHP\Value\Implementation\DateTime;

use DateTimeImmutable;
use StrictlyPHP\Value\Contracts\DateTime\DateTimeInterface;
use StrictlyPHP\Value\Contracts\DateTime\TimezoneInterface;
use StrictlyPHP\Value\Contracts\ValueObjectInterface;

class DateTimeUtc implements DateTimeInterface
{
    public const TIME_ZONE_UTC = 'UTC';

    public const INTERVAL_ONE_DAY = 86400;

    public const INTERVAL_ONE_WEEK = 604800;

    public const INTERVAL_THREE_MONTHS = 7884000;

    public const INTERVAL_ONE_YEAR = 31536000;

    public const INTERVAL_FIFTEEN_MINUTES = 900;

    public const INTERVAL_TWO_HOURS = 7200;

    public const INTERVAL_FIVE_MINUTES = 300;

    private DateTimeImmutable $dateTimeImmutable;

    private function __construct(DateTimeImmutable $dateTimeImmutable)
    {
        $this->dateTimeImmutable = $dateTimeImmutable;
    }

    public static function fromString(string $time, null|TimezoneInterface|string $timezone = null): self
    {
        $timezone = isset($timezone) ? new \DateTimeZone($timezone instanceof TimezoneInterface ? $timezone->getValue() : $timezone) : null;
        $dateTimeImmutable = (new DateTimeImmutable($time, $timezone))
            ->setTimezone(new \DateTimeZone(static::TIME_ZONE_UTC));

        return new DateTimeUtc($dateTimeImmutable);
    }

    public static function fromInterval(int $seconds, DateTimeUtc $dateTimeFrom = null): self
    {
        if (empty($dateTimeFrom)) {
            $dateTimeFrom = DateTimeUtc::fromNow();
        }
        return $dateTimeFrom->addInterval($seconds);
    }

    public static function fromPhpDateTime(\DateTime $date): self
    {
        return DateTimeUtc::fromString($date->format('c'));
    }

    public static function fromNow(): self
    {
        return DateTimeUtc::fromString('now');
    }

    public static function fromUnixEpoch(): self
    {
        return static::fromTimestamp(0);
    }

    public static function fromTimestamp(int $timestamp): self
    {
        return DateTimeUtc::fromString((new \DateTime(sprintf('@%d', $timestamp)))->format('c'));
    }

    public static function fromTimestampMs(int $milliseconds): self
    {
        $timestamp = $milliseconds > 0 ? intval($milliseconds / 1000) : 0;
        return self::fromTimestamp($timestamp);
    }

    public function addInterval(int $seconds): self
    {
        return new self(
            $this->dateTimeImmutable->add(
                new \DateInterval(sprintf('PT%dS', $seconds))
            )
        );
    }

    public function removeInterval(int $seconds): self
    {
        return new self(
            $this->dateTimeImmutable->sub(
                new \DateInterval(sprintf('PT%dS', $seconds))
            )
        );
    }

    public function firstDayOfLastMonth(TimezoneInterface $timezone, string $time = '00:00:00'): self
    {
        return DateTimeUtc::fromString(
            $this->toPhpDateTime()
                ->setTimezone(new \DateTimeZone($timezone->getValue()))
                ->modify('first day of last month')
                ->format(sprintf('Y-m-d %s', $time)),
            $timezone
        );
    }

    public function firstDayOfNextMonth(TimezoneInterface $timezone, string $time = '00:00:00'): self
    {
        return DateTimeUtc::fromString(
            $this->toPhpDateTime()
                ->setTimezone(new \DateTimeZone($timezone->getValue()))
                ->modify('first day of next month')
                ->format(sprintf('Y-m-d %s', $time)),
            $timezone
        );
    }

    public function lastDayOfThisMonth(TimezoneInterface $timezone, string $time = '00:00:00'): self
    {
        return DateTimeUtc::fromString(
            $this->toPhpDateTime()
                ->setTimezone(new \DateTimeZone($timezone->getValue()))
                ->modify('last day of this month')
                ->format(sprintf('Y-m-d %s', $time)),
            $timezone
        );
    }

    public function firstDayOfThisMonth(TimezoneInterface $timezone, string $time = '00:00:00'): self
    {
        return DateTimeUtc::fromString(
            $this->toPhpDateTime()
                ->setTimezone(new \DateTimeZone($timezone->getValue()))
                ->modify('first day of this month')
                ->format(sprintf('Y-m-d %s', $time)),
            $timezone
        );
    }

    public function format(string $format, TimezoneInterface $timezone): string
    {
        return $this->dateTimeImmutable->setTimezone(new \DateTimeZone($timezone->getValue()))->format($format);
    }

    public function toPhpDateTime(): \DateTime
    {
        return new \DateTime($this->getValue());
    }

    public function getValue(): string
    {
        return $this->dateTimeImmutable->format('c');
    }

    public function getTimestamp(): int
    {
        return $this->dateTimeImmutable->getTimestamp();
    }

    public function isBefore(DateTimeInterface $compareDate, bool $inclusive = false): bool
    {
        if ($inclusive) {
            return $this->getTimestamp() <= $compareDate->getTimestamp();
        }

        return $this->getTimestamp() < $compareDate->getTimestamp();
    }

    public function isAfter(DateTimeInterface $compareDate, bool $inclusive = false): bool
    {
        if ($inclusive) {
            return $this->getTimestamp() >= $compareDate->getTimestamp();
        }

        return $this->getTimestamp() > $compareDate->getTimestamp();
    }

    public function jsonSerialize(): string
    {
        return $this->getValue();
    }

    public function __toString(): string
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
}
