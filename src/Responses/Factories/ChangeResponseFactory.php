<?php

namespace Apilayer\Coinlayer\Responses\Factories;

use Apilayer\Coinlayer\Exceptions\CoinlayerException;
use Apilayer\Coinlayer\Responses\Change;
use Apilayer\Coinlayer\Responses\DataAbstractResponse;
use Apilayer\Coinlayer\Exceptions\ApiFailedResponseException;
use Exception;

/**
 * @psalm-import-type _Change from \Apilayer\Coinlayer\CoinlayerClient
 * @psalm-import-type _ApiFailed from \Apilayer\Coinlayer\CoinlayerClient
 * @template-implements ResponseFactoryInterface<_Change>
 */
class ChangeResponseFactory implements ResponseFactoryInterface
{
    /** @use ResponseFactoryTrait<_Change|_ApiFailed> */
    use ResponseFactoryTrait;

    /**
     * @return Change
     * @throws CoinlayerException
     * @throws Exception
     *
     * @psalm-param _Change|_ApiFailed $rawResponse
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

        /** @psalm-var _Change $rawResponse */

        return new Change(
            $rawResponse['success'],
            $rawResponse['terms'],
            $rawResponse['privacy'],
            $rawResponse['change'],
            $rawResponse['start_date'],
            $rawResponse['end_date'],
            $rawResponse['target'],
            $rawResponse['rates']
        );
    }
}
