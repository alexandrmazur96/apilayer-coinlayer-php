<?php

namespace Apilayer\Coinlayer\Actions;

use Apilayer\Coinlayer\Responses\Factories\HistoricalResponseFactory;
use Apilayer\Coinlayer\Exceptions\InvalidArgumentException;
use Apilayer\Coinlayer\Responses\Factories\ResponseFactoryInterface;
use DateTimeInterface;
use Apilayer\Coinlayer\Enums\TargetCurrency;
use Apilayer\Coinlayer\Enums\CryptoCurrency;

/**
 * @psalm-type _HistoricalActionData=array{
 *      target?:TargetCurrency::*,
 *      symbols?:non-empty-list<CryptoCurrency::*>,
 *      expand?:0|1,
 *      callback?:string
 * }
 */
class Historical implements ActionInterface
{
    use ActionAssertTrait;

    private DateTimeInterface $historicalDate;
    /** @psalm-var TargetCurrency::*|null */
    private ?string $target;
    /** @psalm-var non-empty-list<CryptoCurrency::*>|null */
    private ?array $symbols;
    private ?bool $expand;
    private ?string $callback;

    /**
     * @param DateTimeInterface $historicalDate
     * @param string|null $target
     * @param array|null $symbols
     * @param bool|null $expand
     * @param string|null $callback
     * @throws InvalidArgumentException
     */
    public function __construct(
        DateTimeInterface $historicalDate,
        ?string $target,
        ?array $symbols,
        ?bool $expand,
        ?string $callback
    ) {
        if ($target !== null) {
            /** @psalm-var TargetCurrency::* $target */
            $this->assertTargetCurrency($target);
        }
        if ($symbols !== null) {
            /** @psalm-var non-empty-list<CryptoCurrency::*> $symbols */
            $this->assertSymbols($symbols);
        }

        $this->historicalDate = $historicalDate;
        $this->target = $target;
        $this->symbols = $symbols;
        $this->expand = $expand;
        $this->callback = $callback;
    }


    public function getEndpoint(): string
    {
        return $this->historicalDate->format('Y-m-d');
    }

    /**
     * @psalm-return _HistoricalActionData
     */
    public function getData(): array
    {
        $request = [];

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
        return new HistoricalResponseFactory();
    }
}
