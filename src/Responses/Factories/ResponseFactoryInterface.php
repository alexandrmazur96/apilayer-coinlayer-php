<?php

namespace Apilayer\Coinlayer\Responses\Factories;

use Apilayer\Coinlayer\Responses\DataAbstractResponse;

/**
 * @psalm-template T
 */
interface ResponseFactoryInterface
{
    /**
     * @psalm-param T $rawResponse
     * @return DataAbstractResponse
     */
    public function create(array $rawResponse): DataAbstractResponse;
}
