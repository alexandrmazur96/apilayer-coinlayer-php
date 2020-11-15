<?php

namespace Apilayer\Coinlayer\Responses;

use DateTimeImmutable;
use Apilayer\Coinlayer\Enums\TargetCurrency;
use Apilayer\Coinlayer\Enums\CryptoCurrency;
use Exception;

/**
 * @psalm-type _TimeframeResponseData=array{
 *      success:bool,
 *      terms:string,
 *      privacy:string,
 *      timeframe:bool,
 *      start_date:string,
 *      end_date:string,
 *      target:TargetCurrency::*,
 *      rates:array<string,array<CryptoCurrency::*,float>>
 * }
 */
class Timeframe extends DataAbstractResponse
{
    private bool $timeframe;
    private DateTimeImmutable $startDate;
    private DateTimeImmutable $endDate;
    /** @psalm-var TargetCurrency::* */
    private string $target;
    /** @psalm-var array<string,array<CryptoCurrency::*,float>> */
    private array $rates;

    /**
     * @param bool $success
     * @param string $terms
     * @param string $privacy
     * @param bool $timeframe
     * @param string $startDate
     * @param string $endDate
     *
     * @throws Exception
     *
     * @psalm-param TargetCurrency::* $target
     * @psalm-param array<string,array<CryptoCurrency::*,float>> $rates
     */
    public function __construct(
        bool $success,
        string $terms,
        string $privacy,
        bool $timeframe,
        string $startDate,
        string $endDate,
        string $target,
        array $rates
    ) {
        parent::__construct($success, $terms, $privacy);
        $this->timeframe = $timeframe;
        // @todo catch and wrap \Exception into own error
        $this->startDate = new DateTimeImmutable($startDate);
        $this->endDate = new DateTimeImmutable($endDate);
        $this->target = $target;
        $this->rates = [];
        foreach ($rates as $date => $rateObj) {
            $this->rates[$date] = [];
            foreach ($rateObj as $cryptoCurrency => $rate) {
                $this->rates[$date][$cryptoCurrency] = $rate;
            }
        }
    }

    /**
     * @return bool
     */
    public function isTimeframe(): bool
    {
        return $this->timeframe;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getStartDate(): DateTimeImmutable
    {
        return $this->startDate;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getEndDate(): DateTimeImmutable
    {
        return $this->endDate;
    }

    /**
     * @psalm-return TargetCurrency::*
     */
    public function getTarget(): string
    {
        return $this->target;
    }

    /**
     * @return array<string,array<CryptoCurrency::*,float>>
     */
    public function getRates(): array
    {
        return $this->rates;
    }

    /**
     * @psalm-return _TimeframeResponseData
     */
    public function toArray(): array
    {
        return [
            'success' => $this->isSuccess(),
            'terms' => $this->getTerms(),
            'privacy' => $this->getPrivacy(),
            'timeframe' => $this->isTimeframe(),
            'start_date' => $this->getStartDate()->format('Y-m-d'),
            'end_date' => $this->getEndDate()->format('Y-m-d'),
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
