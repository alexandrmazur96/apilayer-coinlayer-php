<?php

namespace Apilayer\Coinlayer\ValueObjects;

use JsonSerializable;

/**
 * @psalm-immutable
 * @psalm-type _ChangeInfo=array{
 *      start_rate:float,
 *      end_rate:float,
 *      change:float,
 *      change_pct:float
 * }
 */
class ChangeInfo implements JsonSerializable
{
    private float $startRate;
    private float $endRate;
    private float $change;
    private float $changePct;

    public function __construct(
        float $startRate,
        float $endRate,
        float $change,
        float $changePct
    ) {
        $this->startRate = $startRate;
        $this->endRate = $endRate;
        $this->change = $change;
        $this->changePct = $changePct;
    }

    /**
     * @return float
     */
    public function getStartRate(): float
    {
        return $this->startRate;
    }

    /**
     * @return float
     */
    public function getEndRate(): float
    {
        return $this->endRate;
    }

    /**
     * @return float
     */
    public function getChange(): float
    {
        return $this->change;
    }

    /**
     * @return float
     */
    public function getChangePct(): float
    {
        return $this->changePct;
    }

    /**
     * @psalm-return _ChangeInfo
     */
    public function jsonSerialize()
    {
        return [
            'start_rate' => $this->getStartRate(),
            'end_rate' => $this->getEndRate(),
            'change' => $this->getChange(),
            'change_pct' => $this->getChangePct(),
        ];
    }
}
