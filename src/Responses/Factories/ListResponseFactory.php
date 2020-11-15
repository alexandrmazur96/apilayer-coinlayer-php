<?php

namespace Apilayer\Coinlayer\Responses\Factories;

use Apilayer\Coinlayer\Exceptions\CoinlayerException;
use Apilayer\Coinlayer\Responses\ListResponse;
use Apilayer\Coinlayer\Responses\DataAbstractResponse;
use Apilayer\Coinlayer\Exceptions\ApiFailedResponseException;

/**
 * @psalm-import-type _List from \Apilayer\Coinlayer\CoinlayerClient
 * @psalm-import-type _ApiFailed from \Apilayer\Coinlayer\CoinlayerClient
 * @template-implements ResponseFactoryInterface<_Convert>
 */
class ListResponseFactory implements ResponseFactoryInterface
{
    /** @use ResponseFactoryTrait<_List|_ApiFailed> */
    use ResponseFactoryTrait;

    /**
     * @return ListResponse
     * @throws CoinlayerException
     *
     * @psalm-param _List|_ApiFailed $rawResponse
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

        /** @psalm-var _List $rawResponse */

        return new ListResponse(
            $rawResponse['success'],
            $rawResponse['crypto'],
            $rawResponse['fiat']
        );
    }
}
