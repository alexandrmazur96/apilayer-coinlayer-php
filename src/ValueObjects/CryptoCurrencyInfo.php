<?php

namespace Apilayer\Coinlayer\ValueObjects;

use JsonSerializable;
use Apilayer\Coinlayer\Enums\CryptoCurrency;

/**
 * @psalm-immutable
 */
class CryptoCurrencyInfo implements JsonSerializable
{
    /** @psalm-var CryptoCurrency::* */
    private string $symbol;
    private string $name;
    private string $nameFull;
    private int $maxSupply;
    private string $iconUrl;

    /**
     * @psalm-param CryptoCurrency::* $symbol
     * @param string $name
     * @param string $nameFull
     * @param int $maxSupply
     * @param string $iconUrl
     */
    public function __construct(
        string $symbol,
        string $name,
        string $nameFull,
        int $maxSupply,
        string $iconUrl
    ) {
        $this->symbol = $symbol;
        $this->name = $name;
        $this->nameFull = $nameFull;
        $this->maxSupply = $maxSupply;
        $this->iconUrl = $iconUrl;
    }

    /**
     * @psalm-return CryptoCurrency::*
     */
    public function getSymbol(): string
    {
        return $this->symbol;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getNameFull(): string
    {
        return $this->nameFull;
    }

    /**
     * @return int
     */
    public function getMaxSupply(): int
    {
        return $this->maxSupply;
    }

    /**
     * @return string
     */
    public function getIconUrl(): string
    {
        return $this->iconUrl;
    }

    /**
     * @psalm-return array{
     *      symbol:CryptoCurrency::*,
     *      name:string,
     *      name_full:string,
     *      max_supply:int,
     *      icon_url:string
     * }
     */
    public function jsonSerialize()
    {
        return [
            'symbol' => $this->getSymbol(),
            'name' => $this->getName(),
            'name_full' => $this->getNameFull(),
            'max_supply' => $this->getMaxSupply(),
            'icon_url' => $this->getIconUrl(),
        ];
    }
}
