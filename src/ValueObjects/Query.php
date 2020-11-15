<?php

namespace Apilayer\Coinlayer\ValueObjects;

use JsonSerializable;
use Apilayer\Coinlayer\Enums\CryptoCurrency;

/**
 * @psalm-immutable
 */
class Query implements JsonSerializable
{
    /** @psalm-var CryptoCurrency::* */
    private string $from;
    /** @psalm-var CryptoCurrency::* */
    private string $to;
    private float $amount;

    /**
     * @psalm-param CryptoCurrency::* $from
     * @psalm-param CryptoCurrency::* $to
     * @param float $amount
     */
    public function __construct(string $from, string $to, float $amount)
    {
        $this->from = $from;
        $this->to = $to;
        $this->amount = $amount;
    }

    /**
     * @psalm-return CryptoCurrency::*
     */
    public function getFrom(): string
    {
        return $this->from;
    }

    /**
     * @psalm-return CryptoCurrency::*
     */
    public function getTo(): string
    {
        return $this->to;
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * @psalm-return array{
     *      from:CryptoCurrency::*,
     *      to:CryptoCurrency::*,
     *      amount:float
     * }
     */
    public function jsonSerialize()
    {
        return [
            'from' => $this->getFrom(),
            'to' => $this->getTo(),
            'amount' => $this->getAmount(),
        ];
    }
}
