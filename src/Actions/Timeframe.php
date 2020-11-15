<?php

namespace Apilayer\Coinlayer\Actions;

use Apilayer\Coinlayer\Responses\Factories\TimeframeResponseFactory;
use Apilayer\Coinlayer\Exceptions\InvalidArgumentException;
use Apilayer\Coinlayer\Responses\Factories\ResponseFactoryInterface;
use DateTimeInterface;
use Apilayer\Coinlayer\Enums\TargetCurrency;
use Apilayer\Coinlayer\Enums\CryptoCurrency;

/**
 * @psalm-type _TimeframeActionData=array{
 *      start_date:string,
 *      end_date:string,
 *      target?:TargetCurrency::*,
 *      symbols?:non-empty-list<CryptoCurrency::*>,
 *      expand?:0|1,
 *      callback?:string
 * }
 */
class Timeframe implements ActionInterface
{
    use ActionAssertTrait;

    private DateTimeInterface $startDate;
    private DateTimeInterface $endDate;

    /** @psalm-var TargetCurrency::*|null  */
    private ?string $target;
    /** @psalm-var non-empty-list<CryptoCurrency::*>|null */
    private ?array $symbols;
    private ?bool $expand;
    private ?string $callback;

    /**
     * @param DateTimeInterface $startDate
     * @param DateTimeInterface $endDate
     * @param string|null $target
     * @param string[]|null $symbols
     * @param bool|null $expand
     * @param string|null $callback
     * @throws InvalidArgumentException
     */
    public function __construct(
        DateTimeInterface $startDate,
        DateTimeInterface $endDate,
        ?string $target,
        ?array $symbols,
        ?bool $expand,
        ?string $callback
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
        $this->expand = $expand;
        $this->callback = $callback;
    }

    /**
     * @psalm-return ActionInterface::ENDPOINT_TIMEFRAME
     */
    public function getEndpoint(): string
    {
        return ActionInterface::ENDPOINT_TIMEFRAME;
    }

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

        if ($this->expand !== null) {
            $request['expand'] = (int) $this->expand;
        }

        if ($this->callback !== null) {
            $request['callback'] = $this->callback;
        }

        return $request;
    }

    public function getResponseFactory(): ResponseFactoryInterface
    {
        return new TimeframeResponseFactory();
    }
}
