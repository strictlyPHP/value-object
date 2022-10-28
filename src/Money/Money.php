<?php

declare(strict_types=1);

namespace StrictlyPHP\Value\Implementation\Money;

use StrictlyPHP\Value\Contracts\Money\CurrencyInterface;
use StrictlyPHP\Value\Contracts\Money\Exception\InvalidCurrencyException;
use StrictlyPHP\Value\Contracts\Money\MoneyInterface;
use StrictlyPHP\Value\Contracts\ValueObjectInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Money\Exception\UnknownCurrencyException;

class Money implements MoneyInterface
{
    private int $amount;

    private CurrencyInterface $currency;

    private function __construct(int $amount, CurrencyInterface $currency)
    {
        $this->amount = $amount;
        $this->currency = $currency;
    }

    public static function fromInt(int $amount, CurrencyInterface $currency): Money
    {
        return new Money($amount, $currency);
    }

    /**
     * @throws InvalidCurrencyException
     */
    public static function fromStrings(string $amount, string $currency): Money
    {
        if (empty($currency)) {
            throw new \InvalidArgumentException('currency cannot be empty');
        }
        $currencies = new \Money\Currencies\ISOCurrencies();
        $moneyParser = new \Money\Parser\DecimalMoneyParser($currencies);
        try {
            $money = $moneyParser->parse($amount, new \Money\Currency($currency));
        } catch (UnknownCurrencyException) {
            throw new InvalidCurrencyException(sprintf('currency %s is not supported', $currency));
        }
        return new self((int) $money->getAmount(), Currency::fromString($money->getCurrency()->getCode()));
    }

    public function format(): string
    {
        /** @var non-empty-string $currencyString */
        $currencyString = $this->currency->getValue();
        $money = new \Money\Money($this->amount, new \Money\Currency($currencyString));
        $currencies = new \Money\Currencies\ISOCurrencies();
        $numberFormatter = new \NumberFormatter('en', \NumberFormatter::CURRENCY);
        $moneyFormatter = new \Money\Formatter\IntlMoneyFormatter($numberFormatter, $currencies);

        return $moneyFormatter->format($money);
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getCurrency(): CurrencyInterface
    {
        return $this->currency;
    }

    public function toArray(): array
    {
        return [
            'amount' => $this->amount,
            'currency' => $this->currency,
        ];
    }

    public function subtract(MoneyInterface $money): Money
    {
        /** @var non-empty-string $currencyString */
        $currencyString = $this->getCurrency()->getValue();
        $moneyPhp = new \Money\Money($this->getAmount(), new \Money\Currency($currencyString));

        /** @var non-empty-string $currencyStringNew */
        $currencyStringNew = $money->getCurrency()->getValue();
        $result = $moneyPhp->subtract(new \Money\Money($money->getAmount(), new \Money\Currency($currencyStringNew)));

        return Money::fromInt((int) $result->getAmount(), Currency::fromString($result->getCurrency()->getCode()));
    }

    public function add(MoneyInterface $money): Money
    {
        /** @var non-empty-string $currencyString */
        $currencyString = $this->getCurrency()->getValue();
        $moneyPhp = new \Money\Money($this->getAmount(), new \Money\Currency($currencyString));

        /** @var non-empty-string $currencyStringNew */
        $currencyStringNew = $money->getCurrency()->getValue();
        $result = $moneyPhp->add(new \Money\Money($money->getAmount(), new \Money\Currency($currencyStringNew)));

        return Money::fromInt((int) $result->getAmount(), Currency::fromString($result->getCurrency()->getCode()));
    }

    /**
     * @return Collection<MoneyInterface>
     */
    public function percent(int ...$percentages): Collection
    {
        /** @var non-empty-string $currencyString */
        $currencyString = $this->getCurrency()->getValue();
        $moneyPhp = new \Money\Money($this->getAmount(), new \Money\Currency($currencyString));

        /** @var non-empty-array<int> $percentages */
        $newMoneyPhpArray = $moneyPhp->allocate($percentages);

        return new ArrayCollection(array_map(
            function (\Money\Money $moneyPhp) {
                return new Money((int) $moneyPhp->getAmount(), Currency::fromString($moneyPhp->getCurrency()->getCode()));
            },
            $newMoneyPhpArray
        ));
    }

    public function jsonSerialize(): array
    {
        return [
            'amount' => $this->amount,
            'currency' => $this->currency,
        ];
    }

    public function isEqual(ValueObjectInterface $compareValueObject): bool
    {
        if (! $compareValueObject instanceof self) {
            return false;
        }
        return $this->jsonSerialize() === $compareValueObject->jsonSerialize();
    }
}
