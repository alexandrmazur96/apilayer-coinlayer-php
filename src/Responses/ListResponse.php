<?php

namespace Apilayer\Coinlayer\Responses;

use Apilayer\Coinlayer\ValueObjects\CryptoCurrencyInfo;
use Apilayer\Coinlayer\Enums\CryptoCurrency;
use Apilayer\Coinlayer\Enums\TargetCurrency;

/**
 * @psalm-type _CryptoInfo=array{
 *      symbol:CryptoCurrency::*,
 *      name:string,
 *      name_full:string,
 *      max_supply:int,
 *      icon_url:string
 * }
 *
 * @psalm-type _ListResponseData=array{
 *      success:bool,
 *      crypto:array<CryptoCurrency::*, CryptoCurrencyInfo>,
 *      fiat:array<TargetCurrency::*,string>
 * }
 */
class ListResponse extends DataAbstractResponse
{
    /** @psalm-var array<CryptoCurrency::*, CryptoCurrencyInfo> */
    private array $crypto;

    /** @psalm-var array<TargetCurrency::*,string> */
    private array $fiat;

    /**
     * @param bool $success
     * @psalm-param array<CryptoCurrency::*,_CryptoInfo> $crypto
     * @psalm-param array<TargetCurrency::*,string> $fiat
     */
    public function __construct(bool $success, array $crypto, array $fiat)
    {
        parent::__construct($success, '', '');
        $this->fiat = $fiat;
        $this->crypto = [];
        foreach ($crypto as $cryptoCurrency => $cryptoCurrencyInfo) {
            $this->crypto[$cryptoCurrency] = new CryptoCurrencyInfo(
                $cryptoCurrencyInfo['symbol'],
                $cryptoCurrencyInfo['name'],
                $cryptoCurrencyInfo['name_full'],
                $cryptoCurrencyInfo['max_supply'],
                $cryptoCurrencyInfo['icon_url']
            );
        }
    }

    /**
     * @psalm-return array<CryptoCurrency::*, CryptoCurrencyInfo>
     */
    public function getCrypto(): array
    {
        return $this->crypto;
    }

    /**
     * @return array<TargetCurrency::*,string>
     */
    public function getFiat(): array
    {
        return $this->fiat;
    }

    /**
     * @psalm-return _ListResponseData
     */
    public function toArray(): array
    {
        return [
            'success' => $this->isSuccess(),
            'crypto' => $this->getCrypto(),
            'fiat' => $this->getFiat(),
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
