<?php

namespace Apilayer\Coinlayer\Responses;

use DateTimeImmutable;
use Apilayer\Coinlayer\Enums\TargetCurrency;
use Apilayer\Coinlayer\Enums\CryptoCurrency;

/**
 * @psalm-type _ListResponseData=array{
 *      success:bool,
 *      terms:string,
 *      privacy:string,
 *      timestamp:int,
 *      target:TargetCurrency::*,
 *      rates:array<CryptoCurrency::*,float>
 * }
 */
class Live extends DataAbstractResponse
{
    private DateTimeImmutable $timestamp;
    /** @psalm-var TargetCurrency::* */
    private string $target;

    /** @psalm-var array<CryptoCurrency::*,float> */
    private array $rates;

    /**
     * @param bool $success
     * @param string $terms
     * @param string $privacy
     * @param int $timestamp
     *
     * @psalm-param TargetCurrency::* $target
     * @psalm-param array<CryptoCurrency::*,float> $rates
     */
    public function __construct(
        bool $success,
        string $terms,
        string $privacy,
        int $timestamp,
        string $target,
        array $rates
    ) {
        parent::__construct($success, $terms, $privacy);
        $this->timestamp = (new DateTimeImmutable())->setTimestamp($timestamp);
        $this->target = $target;
        $this->rates = [];
        foreach ($rates as $cryptoCurrency => $rate) {
            $this->rates[$cryptoCurrency] = $rate;
        }
    }

    public function getTimestamp(): DateTimeImmutable
    {
        return $this->timestamp;
    }

    /**
     * @psalm-return TargetCurrency::*
     */
    public function getTarget(): string
    {
        return $this->target;
    }

    /**
     * @psalm-return array<CryptoCurrency::*,float>
     */
    public function getRates(): array
    {
        return $this->rates;
    }

    /**
     * @psalm-return _ListResponseData
     */
    public function toArray(): array
    {
        return [
            'success' => $this->isSuccess(),
            'terms' => $this->getTerms(),
            'privacy' => $this->getPrivacy(),
            'timestamp' => $this->getTimestamp()->getTimestamp(),
            'target' => $this->getTarget(),
            'rates' => $this->getRates(),
        ];
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
