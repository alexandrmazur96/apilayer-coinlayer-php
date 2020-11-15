<?php

namespace Apilayer\Coinlayer\Actions;

use Apilayer\Coinlayer\Responses\Factories\ConvertResponseFactory;
use Apilayer\Coinlayer\Exceptions\InvalidArgumentException;
use Apilayer\Coinlayer\Responses\Factories\ResponseFactoryInterface;
use DateTimeInterface;
use Apilayer\Coinlayer\Enums\CryptoCurrency;

/**
 * @psalm-type _ConvertActionData=array{
 *      from:CryptoCurrency::*,
 *      to:CryptoCurrency::*,
 *      amount:float,
 *      date?:string,
 *      callback?:string
 * }
 */
class Convert implements ActionInterface
{
    use ActionAssertTrait;

    private string $from;
    private string $to;
    private float $amount;
    private ?DateTimeInterface $date;
    private ?string $callback;

    /**
     * @param string $from
     * @param string $to
     * @param float $amount
     * @param DateTimeInterface|null $date
     * @param string|null $callback
     * @throws InvalidArgumentException
     */
    public function __construct(
        string $from,
        string $to,
        float $amount,
        ?DateTimeInterface $date,
        ?string $callback
    ) {
        $this->assertSymbol($from);
        $this->assertSymbol($to);
        $this->assertAmount($amount);

        /**
         * @psalm-var CryptoCurrency::* $from
         * @psalm-var CryptoCurrency::* $to
         */

        $this->from = $from;
        $this->to = $to;
        $this->amount = $amount;
        $this->date = $date;
        $this->callback = $callback;
    }

    /**
     * @psalm-return ActionInterface::ENDPOINT_CONVERT
     */
    public function getEndpoint(): string
    {
        return ActionInterface::ENDPOINT_CONVERT;
    }

    /**
     * @psalm-return _ConvertActionData
     */
    public function getData(): array
    {
        $request = [
            'from' => $this->from,
            'to' => $this->to,
            'amount' => $this->amount,
        ];

        if ($this->date !== null) {
            $request['date'] = $this->date->format('Y-m-d');
        }

        if ($this->callback !== null) {
            $request['callback'] = $this->callback;
        }

        return $request;
    }

    public function getResponseFactory(): ResponseFactoryInterface
    {
        return new ConvertResponseFactory();
    }
}
