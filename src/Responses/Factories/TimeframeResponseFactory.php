<?php

namespace Apilayer\Coinlayer\Responses\Factories;

use Apilayer\Coinlayer\Exceptions\CoinlayerException;
use Apilayer\Coinlayer\Responses\Timeframe;
use Apilayer\Coinlayer\Responses\DataAbstractResponse;
use Apilayer\Coinlayer\Exceptions\ApiFailedResponseException;
use Exception;

/**
 * @psalm-import-type _Timeframe from \Apilayer\Coinlayer\CoinlayerClient
 * @psalm-import-type _ApiFailed from \Apilayer\Coinlayer\CoinlayerClient
 * @template-implements ResponseFactoryInterface<_Convert>
 */
class TimeframeResponseFactory implements ResponseFactoryInterface
{
    /** @use ResponseFactoryTrait<_Timeframe|_ApiFailed> */
    use ResponseFactoryTrait;

    /**
     * @return DataAbstractResponse
     * @throws CoinlayerException
     * @throws Exception
     *
     * @psalm-param _Timeframe|_ApiFailed $rawResponse
     */
    public function create(array $rawResponse): DataAbstractResponse
    {
        try {
            $this->validate($rawResponse);
        } catch (ApiFailedResponseException $e) {
            /** @psalm-var _ApiFailed $rawResponse */
            $rawResponse = $e->getRawErrorResponse();
            throw new CoinlayerException($e->getMessage(), $e->getCode(), $e, $rawResponse['error']['type']);
        }

        /** @psalm-var _Timeframe $rawResponse */

        return new Timeframe(
            $rawResponse['success'],
            $rawResponse['terms'],
            $rawResponse['privacy'],
            $rawResponse['timeframe'],
            $rawResponse['start_date'],
            $rawResponse['end_date'],
            $rawResponse['target'],
            $rawResponse['rates']
        );
    }
}
