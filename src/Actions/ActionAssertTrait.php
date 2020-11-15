<?php

namespace Apilayer\Coinlayer\Actions;

use Apilayer\Coinlayer\Enums\CryptoCurrency;
use Apilayer\Coinlayer\Enums\TargetCurrency;
use DateTimeInterface;
use Apilayer\Coinlayer\Exceptions\InvalidArgumentException;

trait ActionAssertTrait
{
    /**
     * @param float $amount
     * @throws InvalidArgumentException
     */
    private function assertAmount(float $amount): void
    {
        if ($amount < 0) {
            throw new InvalidArgumentException(sprintf('Amount [%s] should be greater than 0.', $amount));
        }
    }

    /**
     * @param DateTimeInterface $startDate
     * @param DateTimeInterface $endDate
     * @throws InvalidArgumentException
     */
    private function assertDates(DateTimeInterface $startDate, DateTimeInterface $endDate): void
    {
        if ($startDate > $endDate) {
            throw new InvalidArgumentException(
                sprintf(
                    'Start date [%s] should be lower than or equal to end date [%s].',
                    $startDate->format('Y-m-d'),
                    $endDate->format('Y-m-d')
                )
            );
        }
    }

    /**
     * @param string $targetCurrency
     * @psalm-assert TargetCurrency::* $targetCurrency
     * @throws InvalidArgumentException
     */
    private function assertTargetCurrency(string $targetCurrency): void
    {
        if (!in_array($targetCurrency, TargetCurrency::getAvailableCurrencies(), true)) {
            throw new InvalidArgumentException(sprintf('Target currency [%s] is not available.', $targetCurrency));
        }
    }

    /**
     * @param string[] $symbols
     * @psalm-assert list<CryptoCurrency::*> $symbols
     * @throws InvalidArgumentException
     */
    private function assertSymbols(array $symbols): void
    {
        if (empty($symbols)) {
            throw new InvalidArgumentException('If symbols passed they should not be empty');
        }

        $diff = array_diff($symbols, CryptoCurrency::getAvailableCurrencies());

        if (!empty($diff)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Crypto currencies (symbols) list contains not available values [%s]',
                    join(', ', $diff)
                )
            );
        }
    }

    /**
     * @param string $symbol
     * @psalm-assert CryptoCurrency::* $symbol
     * @throws InvalidArgumentException
     */
    private function assertSymbol(string $symbol): void
    {
        if (!in_array($symbol, CryptoCurrency::getAvailableCurrencies(), true)) {
            throw new InvalidArgumentException(sprintf("`%s` symbol is not available.", $symbol));
        }
    }
}
