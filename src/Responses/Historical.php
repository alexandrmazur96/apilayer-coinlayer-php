<?php

namespace Apilayer\Coinlayer\Responses;

use DateTimeImmutable;
use Exception;
use Apilayer\Coinlayer\Enums\TargetCurrency;
use Apilayer\Coinlayer\Enums\CryptoCurrency;

/**
 * @psalm-type _HistoricalResponseData=array{
 *      success:bool,
 *      terms:string,
 *      privacy:string,
 *      timestamp:int,
 *      target:TargetCurrency::*,
 *      historical:bool,
 *      date:string,
 *      rates:array<CryptoCurrency::*,float>
 * }
 */
class Historical extends DataAbstractResponse
{
    private DateTimeImmutable $timestamp;
    /** @psalm-var TargetCurrency::* */
    private string $target;
    private bool $historical;
    private DateTimeImmutable $date;

    /** @psalm-var array<CryptoCurrency::*,float> */
    private array $rates;

    /**
     * @param bool $success
     * @param string $terms
     * @param string $privacy
     * @param int $timestamp
     * @param bool $historical
     * @param string $date
     *
     * @throws Exception
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
        bool $historical,
        string $date,
        array $rates
    ) {
        parent::__construct($success, $terms, $privacy);
        $this->timestamp = (new DateTimeImmutable())->setTimestamp($timestamp);
        $this->target = $target;
        $this->historical = $historical;
        $this->date = new DateTimeImmutable($date);
        $this->rates = [];
        foreach ($rates as $cryptoCurrency => $rate) {
            $this->rates[$cryptoCurrency] = $rate;
        }
    }

    /**
     * @return DateTimeImmutable
     */
    public function getTimestamp(): DateTimeImmutable
    {
        return $this->timestamp;
    }

    /**
     * @return string
     */
    public function getTarget(): string
    {
        return $this->target;
    }

    /**
     * @return bool
     */
    public function isHistorical(): bool
    {
        return $this->historical;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    /**
     * @psalm-return array<CryptoCurrency::*,float>
     */
    public function getRates(): array
    {
        return $this->rates;
    }

    /**
     * @psalm-return _HistoricalResponseData
     */
    public function toArray(): array
    {
        return [
            'success' => $this->isSuccess(),
            'terms' => $this->getTerms(),
            'privacy' => $this->getPrivacy(),
            'timestamp' => $this->getTimestamp()->getTimestamp(),
            'target' => $this->getTarget(),
            'historical' => $this->isHistorical(),
            'date' => $this->getDate()->format('Y-m-d'),
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
