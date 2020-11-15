<?php

namespace Apilayer\Coinlayer\Responses;

use Apilayer\Coinlayer\ValueObjects\ChangeInfo;
use DateTimeImmutable;
use Apilayer\Coinlayer\Enums\TargetCurrency;
use Apilayer\Coinlayer\Enums\CryptoCurrency;
use Exception;

/**
 * @psalm-type _RateInfo=array{
 *      start_rate:float,
 *      end_rate:float,
 *      change:float,
 *      change_pct:float
 * }
 *
 * @psalm-type _ChangeResponseData=array{
 *      success:bool,
 *      terms:string,
 *      privacy:string,
 *      change:bool,
 *      start_date:string,
 *      end_date:string,
 *      target:TargetCurrency::*,
 *      rates:array<CryptoCurrency::*,ChangeInfo>
 * }
 */
class Change extends DataAbstractResponse
{
    private bool $change;
    private DateTimeImmutable $startDate;
    private DateTimeImmutable $endDate;
    /** @psalm-var TargetCurrency::* */
    private string $target;

    /** @psalm-var array<CryptoCurrency::*,ChangeInfo> */
    private array $rates;

    /**
     * @param bool $success
     * @param string $terms
     * @param string $privacy
     * @param bool $change
     * @param string $startDate
     * @param string $endDate
     *
     * @throws Exception
     *
     * @psalm-param TargetCurrency::* $target
     * @psalm-param array<CryptoCurrency::*,_RateInfo> $rates
     */
    public function __construct(
        bool $success,
        string $terms,
        string $privacy,
        bool $change,
        string $startDate,
        string $endDate,
        string $target,
        array $rates
    ) {
        parent::__construct($success, $terms, $privacy);
        $this->change = $change;
        $this->startDate = new DateTimeImmutable($startDate);
        $this->endDate = new DateTimeImmutable($endDate);
        $this->target = $target;
        $this->rates = [];

        foreach ($rates as $cryptoCurrency => $changeInfo) {
            $this->rates[$cryptoCurrency] = new ChangeInfo(
                $changeInfo['start_rate'],
                $changeInfo['end_rate'],
                $changeInfo['change'],
                $changeInfo['change_pct']
            );
        }
    }

    /**
     * @return bool
     */
    public function isChange(): bool
    {
        return $this->change;
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
     * @psalm-return array<CryptoCurrency::*,ChangeInfo>
     */
    public function getRates(): array
    {
        return $this->rates;
    }

    /**
     * @psalm-return _ChangeResponseData
     */
    public function toArray(): array
    {
        return [
            'success' => $this->isSuccess(),
            'terms' => $this->getTerms(),
            'privacy' => $this->getPrivacy(),
            'change' => $this->isChange(),
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
