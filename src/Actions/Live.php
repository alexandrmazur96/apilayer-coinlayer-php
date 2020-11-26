<?php

namespace Apilayer\Coinlayer\Actions;

use Apilayer\Coinlayer\Responses\Factories\LiveResponseFactory;
use Apilayer\Coinlayer\Exceptions\InvalidArgumentException;
use Apilayer\Coinlayer\Responses\Factories\ResponseFactoryInterface;
use Apilayer\Coinlayer\Enums\TargetCurrency;
use Apilayer\Coinlayer\Enums\CryptoCurrency;

/**
 * @psalm-type _LiveActionData=array{
 *      target?:TargetCurrency::*,
 *      symbols?:non-empty-list<CryptoCurrency::*>,
 *      expand?:0|1,
 *      callback?:string
 * }
 */
class Live implements ActionInterface
{
    use ActionAssertTrait;

    /** @psalm-var TargetCurrency::*|null */
    private ?string $target;
    /** @psalm-var non-empty-list<CryptoCurrency::*>|null */
    private ?array $symbols;
    private ?bool $expand;
    private ?string $callback;

    /**
     * @param string|null $target
     * @param string[]|null $symbols
     * @param bool|null $expand
     * @param string|null $callback
     * @throws InvalidArgumentException
     */
    public function __construct(
        ?string $target = null,
        ?array $symbols = null,
        ?bool $expand = null,
        ?string $callback = null
    ) {
        if ($target !== null) {
            /** @psalm-var TargetCurrency::* $target */
            $this->assertTargetCurrency($target);
        }
        if ($symbols !== null) {
            /** @psalm-var non-empty-list<CryptoCurrency::*> $symbols */
            $this->assertSymbols($symbols);
        }

        $this->target = $target;
        $this->symbols = $symbols;
        $this->expand = $expand;
        $this->callback = $callback;
    }

    /**
     * @psalm-return ActionInterface::ENDPOINT_LIVE
     */
    public function getEndpoint(): string
    {
        return ActionInterface::ENDPOINT_LIVE;
    }

    /**
     * @psalm-return _LiveActionData
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
            $request['expand'] = (int)$this->expand;
        }

        if ($this->callback !== null) {
            $request['callback'] = $this->callback;
        }

        return $request;
    }

    public function getResponseFactory(): ResponseFactoryInterface
    {
        return new LiveResponseFactory();
    }
}
