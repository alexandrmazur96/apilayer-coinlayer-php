<?php

namespace Apilayer\Coinlayer\Responses\Factories;

use Apilayer\Coinlayer\Exceptions\CoinlayerErrorCodes;
use Apilayer\Coinlayer\Exceptions\CoinlayerException;
use Apilayer\Coinlayer\Responses\Live;
use Apilayer\Coinlayer\Responses\DataAbstractResponse;
use Apilayer\Coinlayer\Exceptions\ApiFailedResponseException;

/**
 * @psalm-import-type _Live from \Apilayer\Coinlayer\CoinlayerClient
 * @psalm-import-type _ApiFailed from \Apilayer\Coinlayer\CoinlayerClient
 * @template-implements ResponseFactoryInterface<_Convert>
 */
class LiveResponseFactory implements ResponseFactoryInterface
{
    /** @use ResponseFactoryTrait<_Live|_ApiFailed> */
    use ResponseFactoryTrait;

    /**
     * @return Live
     * @throws CoinlayerException
     *
     * @psalm-param _Live|_ApiFailed $rawResponse
     */
    public function create(array $rawResponse): DataAbstractResponse
    {
        try {
            $this->validate($rawResponse);
        } catch (ApiFailedResponseException $e) {
            /** @psalm-var _ApiFailed $rawResponse */
            $rawResponse = $e->getRawErrorResponse();
            throw new CoinlayerException(
                $e->getMessage(),
                $e->getCode(),
                $e,
                $rawResponse['error']['type'] ?? CoinlayerErrorCodes::TYPE_INTERNAL_LIB_ERROR
            );
        }

        /** @psalm-var _Live $rawResponse */

        return new Live(
            $rawResponse['success'],
            $rawResponse['terms'],
            $rawResponse['privacy'],
            $rawResponse['timestamp'],
            $rawResponse['target'],
            $rawResponse['rates']
        );
    }
}
