<?php

namespace Apilayer\Coinlayer\ValueObjects;

use JsonSerializable;

/**
 * @psalm-immutable
 */
class CoinlayerInfo implements JsonSerializable
{
    private int $timestamp;
    private float $rate;

    /**
     * @param int $timestamp
     * @param float $rate
     */
    public function __construct(int $timestamp, float $rate)
    {
        $this->timestamp = $timestamp;
        $this->rate = $rate;
    }

    /**
     * @return int
     */
    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    /**
     * @return float
     */
    public function getRate(): float
    {
        return $this->rate;
    }

    /**
     * @psalm-return array{timestamp:int,rate:float}
     */
    public function jsonSerialize()
    {
        return [
            'timestamp' => $this->getTimestamp(),
            'rate' => $this->getRate(),
        ];
    }
}
