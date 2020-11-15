<?php

namespace Apilayer\Coinlayer;

use Apilayer\Coinlayer\Actions\ActionInterface;
use Apilayer\Coinlayer\Exceptions\CoinlayerException;
use Apilayer\Coinlayer\Responses\DataAbstractResponse;
use Apilayer\Coinlayer\Enums\HttpSchema;
use Apilayer\Coinlayer\Exceptions\InvalidArgumentException;
use JsonException;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Apilayer\Coinlayer\Exceptions\CoinlayerErrorCodes;
use Apilayer\Coinlayer\Enums\TargetCurrency;
use Apilayer\Coinlayer\Enums\CryptoCurrency;

/**
 * @psalm-type _CryptoCurrencyInfo=array{
 *      symbol:CryptoCurrency::*,
 *      name:string,
 *      name_full:string,
 *      max_supply:int,
 *      icon_url:string
 * }
 *
 * @psalm-type _Query=array{
 *      from:CryptoCurrency::*,
 *      to:CryptoCurrency::*,
 *      amount:float
 * }
 *
 * @psalm-type _Info=array{
 *      timestamp:int,
 *      rate:float
 * }
 *
 * @psalm-type _ApiFailed=array{
 *      success:bool,
 *      error:array{
 *          code:CoinlayerErrorCodes::CODE_*,
 *          type:CoinlayerErrorCodes::TYPE_*,
 *          info:string
 *      }
 * }
 *
 * @psalm-type _List=array{
 *      success:bool,
 *      crypto:array<CryptoCurrency::*, _CryptoCurrencyInfo>,
 *      fiat:array<TargetCurrency::*, CryptoCurrency::*>
 * }
 *
 * @psalm-type _Live=array{
 *      success:bool,
 *      terms:string,
 *      privacy:string,
 *      timestamp:int,
 *      target:TargetCurrency::*,
 *      rates:array<CryptoCurrency::*,float>
 * }
 *
 * @psalm-type _Historical=array{
 *      success:bool,
 *      terms:string,
 *      privacy:string,
 *      timestamp:int,
 *      target:TargetCurrency::*,
 *      historical:bool,
 *      date:string,
 *      rates:array<CryptoCurrency::*,float>
 * }
 *
 * @psalm-type _Convert=array{
 *      success:bool,
 *      terms:string,
 *      privacy:string,
 *      query:_Query,
 *      info:_Info,
 *      result:float
 * }
 *
 * @psalm-type _Timeframe=array{
 *      success:bool,
 *      terms:string,
 *      privacy:string,
 *      timeframe:bool,
 *      start_date:string,
 *      end_date:string,
 *      target:TargetCurrency::*,
 *      rates:array<string,array<CryptoCurrency::*,float>>
 * }
 *
 * @psalm-type _Change=array{
 *      success:bool,
 *      terms:string,
 *      privacy:string,
 *      change:bool,
 *      start_date:string,
 *      end_date:string,
 *      target:TargetCurrency::*,
 *      rates:array<CryptoCurrency::*,array{start_rate:float,end_rate:float,change:float,change_pct:float}>
 * }
 */
class CoinlayerClient extends BaseClient
{
    /**
     * @param ClientInterface $httpClient
     * @param RequestFactoryInterface $httpRequestFactory
     * @param string $apiKey
     * @param string $schema
     * @throws InvalidArgumentException
     */
    public function __construct(
        ClientInterface $httpClient,
        RequestFactoryInterface $httpRequestFactory,
        string $apiKey,
        string $schema = HttpSchema::SCHEMA_HTTP
    ) {
        $this->apiKey = $apiKey;
        $this->httpRequestFactory = $httpRequestFactory;
        $this->httpClient = $httpClient;
        if (!in_array($schema, [HttpSchema::SCHEMA_HTTP, HttpSchema::SCHEMA_HTTPS], true)) {
            throw new InvalidArgumentException('Invalid schema passed!');
        }
        $this->schema = $schema;
    }

    /**
     * @param ActionInterface $action
     * @return DataAbstractResponse
     * @throws CoinlayerException
     */
    public function perform(ActionInterface $action): DataAbstractResponse
    {
        $apiUrl = $this->buildApiUrl($action->getEndpoint(), $this->prepareData($action));

        $request = $this->httpRequestFactory->createRequest(
            'GET',
            $apiUrl
        );

        try {
            $response = $this->httpClient->sendRequest($request);

            /** @psalm-var _ApiFailed|_List|_Live|_Historical|_Convert|_Timeframe|_Change $rawResponse */
            $rawResponse = json_decode(
                $response->getBody()->getContents(),
                true,
                512,
                JSON_THROW_ON_ERROR
            );
        } catch (JsonException | ClientExceptionInterface $e) {
            throw new CoinlayerException($e->getMessage(), $e->getCode(), $e->getPrevious());
        }

        $responseFactory = $action->getResponseFactory();

        return $responseFactory->create($rawResponse);
    }

    public function getApiBaseUrl(): string
    {
        return 'api.coinlayer.com';
    }
}
