<?php

namespace Apilayer\Coinlayer\Responses\Factories;

use Apilayer\Coinlayer\Exceptions\CoinlayerException;
use Apilayer\Coinlayer\Responses\Convert;
use Apilayer\Coinlayer\Responses\DataAbstractResponse;
use Apilayer\Coinlayer\Exceptions\ApiFailedResponseException;

/**
 * @psalm-import-type _Convert from \Apilayer\Coinlayer\CoinlayerClient
 * @psalm-import-type _ApiFailed from \Apilayer\Coinlayer\CoinlayerClient
 * @template-implements ResponseFactoryInterface<_Convert>
 */
class ConvertResponseFactory implements ResponseFactoryInterface
{
    /** @use ResponseFactoryTrait<_Convert|_ApiFailed> */
    use ResponseFactoryTrait;

    /**
     * @return Convert
     * @throws CoinlayerException
     *
     * @psalm-param _ApiFailed|_Convert $rawResponse
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

        /** @psalm-var _Convert $rawResponse */

        return new Convert(
            $rawResponse['success'],
            $rawResponse['terms'],
            $rawResponse['privacy'],
            $rawResponse['query'],
            $rawResponse['info'],
            $rawResponse['result']
        );
    }
}
