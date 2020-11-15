<?php

namespace Apilayer\Coinlayer\Responses\Factories;

use Apilayer\Coinlayer\Exceptions\CoinlayerException;
use Apilayer\Coinlayer\Responses\Historical;
use Apilayer\Coinlayer\Responses\DataAbstractResponse;
use Apilayer\Coinlayer\Exceptions\ApiFailedResponseException;
use Exception;

/**
 * @psalm-import-type _Historical from \Apilayer\Coinlayer\CoinlayerClient
 * @psalm-import-type _ApiFailed from \Apilayer\Coinlayer\CoinlayerClient
 * @template-implements ResponseFactoryInterface<_Historical>
 */
class HistoricalResponseFactory implements ResponseFactoryInterface
{
    /** @use ResponseFactoryTrait<_Historical|_ApiFailed> */
    use ResponseFactoryTrait;

    /**
     * @return Historical
     * @throws CoinlayerException
     * @throws Exception
     * @todo wrap \Exception to the own API error.
     *
     * @psalm-param _Historical|_ApiFailed $rawResponse
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

        /** @psalm-var _Historical $rawResponse */

        return new Historical(
            $rawResponse['success'],
            $rawResponse['terms'],
            $rawResponse['privacy'],
            $rawResponse['timestamp'],
            $rawResponse['target'],
            $rawResponse['historical'],
            $rawResponse['date'],
            $rawResponse['rates']
        );
    }
}
