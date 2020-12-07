<?php

namespace Apilayer\Coinlayer\Actions;

use Apilayer\Coinlayer\Responses\Factories\ChangeResponseFactory;
use Apilayer\Coinlayer\Exceptions\InvalidArgumentException;
use Apilayer\Coinlayer\Responses\Factories\ResponseFactoryInterface;
use DateTimeInterface;
use Apilayer\Coinlayer\Enums\TargetCurrency;
use Apilayer\Coinlayer\Enums\CryptoCurrency;

/**
 * @psalm-type _ChangeActionData=array{
 *      start_date:string,
 *      end_date:string,
 *      target?:TargetCurrency::*,
 *      symbols?:non-empty-list<CryptoCurrency::*>
 * }
 */
class Change implements ActionInterface
{
    use ActionAssertTrait;

    private DateTimeInterface $startDate;
    private DateTimeInterface $endDate;
    /** @psalm-var TargetCurrency::*|null */
    private ?string $target;
    /** @psalm-var non-empty-list<CryptoCurrency::*>|null */
    private ?array $symbols;

    /**
     * @param DateTimeInterface $startDate
     * @param DateTimeInterface $endDate
     * @param string|null $target
     * @param string[]|null $symbols
     * @throws InvalidArgumentException
     */
    public function __construct(
        DateTimeInterface $startDate,
        DateTimeInterface $endDate,
        ?string $target = null,
        ?array $symbols = null
    ) {
        $this->assertDates($startDate, $endDate);
        if ($target !== null) {
            /** @psalm-var TargetCurrency::* $target */
            $this->assertTargetCurrency($target);
        }
        if ($symbols !== null) {
            /** @psalm-var non-empty-list<CryptoCurrency::*> $symbols */
            $this->assertSymbols($symbols);
        }

        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->target = $target;
        $this->symbols = $symbols;
    }

    /**
     * @psalm-return ActionInterface::ENDPOINT_CHANGE
     */
    public function getEndpoint(): string
    {
        return ActionInterface::ENDPOINT_CHANGE;
    }

    /**
     * @psalm-return _ChangeActionData
     */
    public function getData(): array
    {
        $request = [
            'start_date' => $this->startDate->format('Y-m-d'),
            'end_date' => $this->endDate->format('Y-m-d'),
        ];

        if ($this->target !== null) {
            $request['target'] = $this->target;
        }

        if ($this->symbols !== null) {
            $request['symbols'] = $this->symbols;
        }

        return $request;
    }

    public function getResponseFactory(): ResponseFactoryInterface
    {
        return new ChangeResponseFactory();
    }
}
